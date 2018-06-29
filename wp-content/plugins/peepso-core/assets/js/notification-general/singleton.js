import $ from 'jquery';
import peepso, { observer } from 'peepso';

let instance;

export default class NotificationGeneral extends peepso.npm.EventEmitter {
    constructor() {
        if ( ! instance ) {
            super();
            instance = this;

            // Notification items.
            // NOTE: First index is deliberately set to empty string for easy page-to-items mapping.
            this.notifications = [ '' ];

            // Unread counter.
            this.unreadCount = null;

            // Request parameters.
            this.params = {
                page: 1,
                per_page: 5,
                unread_only: 0
            };

            // Flag to indicate that all notification items are already loaded.
            this.fetchEnd = false;

            // Handle update notifications.
            observer.addAction( 'notification_update', json => {
                this.doUpdate( json );
            }, 10, 1 );
        }

        return instance;
    }

    /**
     * Get HTML representation on the notification items.
     * @returns {string}
     */
    get html() {
        let notification = _.flatten( this.notifications ),
            html = notification.join( '' );

        return html;
    }

    /**
     * Update notification items if needed.
     * @param {Object} json
     * @fires NotificationGeneral#counter:updated
     */
    doUpdate( json ) {
        let key = 'ps-js-notifications',
            data, unreadCount;

        if ( json && json.data ) {
            data = json.data[ key ] || {};
            unreadCount = +data.count;

            // Check if we really need to update the notification items, and skip if we don't.
            if ( ! _.isNaN( unreadCount ) && this.unreadCount !== unreadCount ) {
                this.unreadCount = unreadCount;
                this.emit( 'counter:updated', unreadCount );
                this.reset();
            }
        }
    }

    /**
     * Reset notification items content.
     * @returns {Promise}
     */
    reset() {
        return new Promise(( resolve ) => {
            this.notifications = [ '' ];
            this.params.page = 1;
            this.fetchEnd = false;
            this.fetch().catch( $.noop ).then(() => {
                resolve();
            });
        });
    }

    /**
     * Fetch notification items.
     * @param {number} [page=null]
     * @returns {Promise}
     * @fires NotificationGeneral#html:updated
     */
    fetch( page = null ) {
        let params = _.extend({}, this.params, { page: page || this.params.page }),
            request, data;

        request = new Promise(( resolve, reject ) => {
            if ( this.fetchEnd ) {
                resolve( [] );
                return;
            }

            peepso.getJson( 'notificationsajax.get_latest', params, json => {
                if ( json.success ) {
                    data = json.data || {};
                    resolve( data.notifications || [] );
                } else if ( json.errors && params.page === 1 ) {
                    reject( json.errors );
                } else {
                    resolve( [] );
                }
            });
        });

        request.then( notifications => {
            if ( _.isArray( notifications ) && notifications.length ) {
                this.notifications[ params.page ] = notifications;
            } else {
                this.fetchEnd = true;
            }
        }).catch( errors => {
            this.fetchEnd = true;
            this.params.page = 1;
            this.notifications = [ '', [
                '<div class="ps-notification">',
                    '<a class="ps-notification__inside">',
                        '<div class="ps-notification__desc">',
                            errors.join( '<br />' ),
                        '</div>',
                    '</a>',
                '</div>'
            ].join( '' ) ];
        }).then(() => {
            this.emit( 'html:updated', this.html );
        });

        return request;
    }

    /**
     * Load next page on notification items.
     * @returns {Promise}
     */
    next() {
        return new Promise(( resolve , reject ) => {
            let page = this.params.page + 1;

            if ( this.fetchEnd ) {
                reject();
                return;
            }

            this.fetch( page ).then(() => {
                this.params.page = page;
                resolve();
            });
        });
    }

    /**
     * Mark notification items as read.
     * @param {number} [id=null]
     * @returns {Promise}
     * @fires NotificationGeneral#counter:updated
     */
    markRead( id = null ) {
        return new Promise(( resolve, reject ) => {
            let params = id ? { note_id: id } : null;

            peepso.postJson( 'notificationsajax.mark_as_read', params, ( json ) => {
                if ( json.success ) {
                    this.unreadCount = id ? Math.max( this.unreadCount - 1, 0 ) : 0;
                    this.notifications = this.__markHtmlAsRead( this.notifications, id );
                    this.emit( 'counter:updated', this.unreadCount );
                    this.emit( 'html:updated', this.html );
                    resolve();
                } else {
                    reject();
                }
            });
        });
    }

    /**
     * Toggle unread notifications only.
     * @returns {Promise}
     * @fires NotificationGeneral#unread_only:updated
     */
    toggleUnreadOnly() {
        return new Promise(( resolve, reject ) => {
            let unreadOnly = this.params.unread_only;

            unreadOnly = unreadOnly ? 0 : 1;
            this.params.unread_only = unreadOnly;
            this.reset().then(() => {
                this.emit( 'unread_only:updated', unreadOnly );
                resolve();
            })
        });
    }

    /**
     * Mark HTML representation of notification items as read.
     * NOTE: This might not ideal but fast enough to mark notification items as read.
     * @param {Array} notifications
     * @param {number} [id=null]
     * @returns {Array}
     * @fires NotificationGeneral#html:updated
     */
    __markHtmlAsRead( notifications, id = null ) {
        let notifClass = 'ps-js-notification',
            unreadClass = 'ps-notification--unread',
            unreadButton = 'ps-js-mark-as-read',
            unreadAttr = 'data-unread';

        return _.map( notifications, ( item ) => {
            if ( _.isArray( item ) ) {
                item = this.__markHtmlAsRead( item, id );
            } else if ( _.isString( item ) && item.length ) {
                let $wrapper = $( '<div/>' ).html( item ),
                    $items = $wrapper.find( '.' + notifClass );

                if ( id ) {
                    $items = $items.filter( '.' + notifClass + '--' + id );
                }

                $items.removeClass( unreadClass ).removeAttr( unreadAttr );
                $items.find( '.' + unreadButton ).remove();
                item = $wrapper.html();
            }

            return item;
        }, this );
    }

}
