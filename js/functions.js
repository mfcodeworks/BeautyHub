$(document).ready(function() {
    // Append active to current nav link
    var page;
    if(document.location.pathname.match(/[^\/]+$/)) page = document.location.pathname.match(/[^\/]+$/)[0];
    else page='index.php';
    //console.log(page);
    $("li[name='"+page+"']").addClass('active');

    /**
     *  Set client timezone
     * 
     */
    var timezone_offset_minutes = new Date().getTimezoneOffset();
    timezone_offset_minutes = timezone_offset_minutes == 0 ? 0 : -timezone_offset_minutes;
    // Timezone difference in minutes such as 330 or -360 or 0
    //console.log("Timezone offset "+timezone_offset_minutes);
    $.post("scripts/set-timezone.php",
    {
        timezone_offset_minutes: timezone_offset_minutes,
    },
    function(data,status) {
        console.log("Timezone name "+data);
    });
    

    // Autocomplete brands
    var options = {
        url: "js/brands.json",
        getValue: "name",
        list: {
            match: {
                enabled: true
            }
        }
    };
    $("#productBrand").easyAutocomplete(options);

    // Print new shade form
    if(page == "detail.php") {
        $('#top').before(" \
            <div id='new-shade-enter' style='width:100%;height:100%;position:fixed;background:rgba(0,0,0,.5);z-index:16;'> \
                <div class='row popup'> \
                    <div class='center-block col-md-12 text-center' style='position: absolute; float: none; background-color: white; top: 50%; border-radius: 1em; padding: 0.5em; z-index:20;'> \
                        <form action='javascript:void(0)' id='submit-new-shade-form'> \
                            <div class='form-group' style='width:80%; margin-left:auto; margin-right:auto;'> \
                                <label for='newShade'><h4>New shade name:</h4></label> \
                                <input type='text' class='form-control' id='newShade' name='newShade'> \
                            </div> \
                            <div class='form-group'> \
                                <input type='submit' class='btn btn-primary' value='Submit new shade'> \
                            </div> \
                        </form> \
                    </div> \
                </div> \
            </div>"
        );
        $('#top').before("\
            <div id='new-dupe-enter' style='width:100%;height:100%;position:fixed;background:rgba(0,0,0,.5);z-index:16;'> \
                <div class='row popup'> \
                    <div class='center-block col-md-12 col-lg-12 col-xs-12 text-center' style='margin-top: 0; position: absolute; float: none; background-color: white; top: 50%; border-radius: 1em; padding: 0.5em; z-index:20;'> \
                        <form action='javascript:void(0)' id='submit-new-dupe-form'> \
                            <div class='form-group' style='width:80%; margin-left:auto; margin-right:auto;' id='thisShadeContainer'> \
                                <label for='thisShade'><h4>Current product shade:</h4></label> \
                                <input type='text' class='form-control' id='thisShade' name='thisShade' placeholder='Ruby'> \
                            </div> \
                            <div class='form-group' style='width:80%; margin-left:auto; margin-right:auto;'> \
                                <label for='dupeName'><h4>Dupe product:</h4></label> \
                                <input type='text' class='form-control' id='dupeName' name='dupeName' placeholder='Cover FX - Custom Enhancer Drops'> \
                            </div> \
                            <div class='form-group' style='width:80%; margin-left:auto; margin-right:auto;'> \
                                <label for='dupeShade'><h4>Dupe product shade:</h4></label> \
                                <input type='text' class='form-control' id='dupeShade' name='dupeShade' placeholder='Leave blank for no shade'> \
                            </div> \
                            <div class='form-group'> \
                                <input type='submit' class='btn btn-primary' value='Submit new dupe'> \
                            </div> \
                        </form> \
                    </div> \
                </div> \
            </div>"
        );
        dupeFormRemover();
    };
    // Hide form until clicked
    $("#new-shade-enter").hide();
    $("#new-dupe-enter").hide();

    // Add new dupe form submit
    $("#submit-new-dupe-form").submit(function(){
        console.log("New dupe added.");
        $.post("scripts/add-new-dupe.php",
        {
            thisShade: $("#thisShade").val(),
            dupeShade: $("#dupeShade").val(),
            dupeName: $("#dupeName").val()
        },
        function(data,status){
            console.log(data);
            $("#new-dupe-enter").hide();
            pageReload();
        });
    })

    //Add new product dupe display
    $("#add-product-dupe").click(function(){
        $("#new-dupe-enter").show();
    });

    // Photo lightbox
    myImgObserver();

    // Show form when button clicked
    $("#add-a-shade").click(function() {
        console.log("Add shade clicked");
        $("#new-shade-enter").show();
    });

    $("body").click(function(e) {
        if( $("#new-shade-enter").css("display") === "block" ) {
            console.log("new-shade-enter visible");
            if( e.target.id == "new-shade-enter") {
                console.log(e.target.id);
                $("#new-shade-enter").hide();
            }
        }
    });

    // Handle new shade form
    $("#submit-new-shade-form").submit(function() {
        console.log("Shade submitted");
        $.post("scripts/add-shade.php",
        {
            id: $("#product-title").attr('name'),
            shade: $("#newShade").val()
        },
        function(data,status){
            console.log(data);
            $("#new-shade-enter").hide();
            pageReload();
        })
    });

    $(".fa-star-o")
        .mouseover(function() {
            var num = $(this).attr('id')[$(this).attr('id').length -1];
            for(var i=0;i<=num;i++) {
                $("#star"+i).removeClass("fa-star-o");
                $("#star"+i).addClass("fa-star");
            }
        })
        .mouseout(function() {
            for(var i=0;i<6;i++){
                if(i>$('#productRating').val()) {
                    $("#star"+i).removeClass("fa-star");
                    $("#star"+i).addClass("fa-star-o");
                }
                else if(i>$('#authorRating').val()) {
                        $("#star"+i).removeClass("fa-star");
                        $("#star"+i).addClass("fa-star-o");
                    }
            }
        })
        .click(function() {
            var num = $(this).attr('id')[$(this).attr('id').length -1];
            $('#productRating').val(num);
            $('#authorRating').val(num);
        });

    //Add product view to counter
    if(page == "detail.php") {
        setTimeout(addProductView, 10000);
    }

    function addProductView() {
        $.post("scripts/add-product-view.php",
        {
            id: $('#product-title').attr('name')
        },
        function(data,status){
        });
    };

    //Send contact email
    $("#contact-form").submit(function() {
        console.log("Email sent");

        $.post("scripts/contact-email.php",
        {
            firstname: $("#firstname").val(),
            lastname: $("#lastname").val(),
            email: $("#email").val(),
            subject: $("#subject").val(),
            message: $("#message").val()
        },
        function(data,status){
            console.log(data);
        });
    });

    //Subscribe email
    $("#subscribe-email-list").submit(function(){
        console.log("Subscribing email");
        $.post("scripts/subscribe-email.php",
        {
            email: $("#subscribe-email").val(),
        },
        function(data,status){
            console.log(data);
        });
    });

    //Add new product
    $('#add-new-product').submit(function() {
        console.log("product submitted");

        var productInfoJSON = scrapeProduct( $('#productBrand').val()+" "+$('#productName').val());
        var productInfo = JSON.parse(productInfoJSON);
        console.log(productInfo);

        if( $('#productName').val() && $('#productBrand').val() && $('#productType').val() ) {
            $.post("scripts/add-new-product.php",
            {
                productName: $('#productName').val(),
                productBrand: $('#productBrand').val(),
                productImg: productInfo.img,
                productDescription: productInfo.description,
                productShades: $('#productShades').val(),
                productType: $('#productType').val(),
                productRating: $('#productRating').val(),
                productSite: $('#productSite').val(),
                productPrice: $('#productPrice').val()
            },
            function(data,status){
                console.log("Product ID:"+data);
                if(data.match(/^[0-9]+$/) != null)  echoAlert("Product saved successfully!");
            });
        }
    });

    // Submit login info
    $('#login-form').submit(function() {
        console.log("Login submit");
        $.post("scripts/process-login.php",
        {
            username: $('#username').val(),
            password: $('#password').val()
        },
        function(data,status){
            console.log(data);
            if(data.indexOf("true") > -1) location.reload();
            else echoAlert("Couldn't login. Check username/password.");
        });
    });

    // Submit login info
    $('#big-login-form').submit(function() {
        console.log("Login submit");
        $.post("scripts/process-login.php",
        {
            username: $('#big-username').val(),
            password: $('#big-password').val()
        },
        function(data,status){
            if(data == "true") location.reload();
            else echoAlert("Couldn't login. Check username/password.");
        });
    });

    // Submit login info
    $('#comment-login-form').submit(function() {
        $.post("scripts/process-comment-login.php",
        {
            username: $('#comment-username').val(),
            password: $('#comment-password').val()
        },
        function(data,status){
            if(data == "false") echoAlert("Couldn't login. Check username/password.");
            else {
                $('#comment-form').empty();
                $('#comment-form').append(data);
                ratingStars();
                commentFormHandler();
            }
        });
    });

    // Submit info to register account
    $('#register-form').submit(function() {
        $("#AJAXalert").remove();
        console.log("Register submit");
        // Check register values
        if($('#reg-username').val() == "" || $('#reg-username').val() == "undefined" || $('#reg-password').val() == "" || $('#reg-password').val() == "undefined" || $('#reg-email').val() == "" || $('#reg-email').val() == "undefined") {
            echoAlert("Registering new account requires a username, password and email.");
            return;
        }
        $.post("scripts/process-register.php",
        {
            username: $('#reg-username').val(),
            password: $('#reg-password').val(),
            email   : $('#reg-email').val()
        },
        function(data,status){
            if(data.indexOf("true") > -1) location.reload();
            else echoAlert("Couldn't register account. Username/email already exists.");
        });
    });

    // Submit info to change password for account
    $('#password-change-form').submit(function() {
        console.log("Password change submit");
        if( $('#password_1').val() == $('#password_2').val() )
        {
            console.log("Password match");
            $.post("scripts/change-password.php",
            {
                oldPassword: $('#password_old').val(),
                newPassword: $('#password_1').val()
            },
            function(data,status){
                console.log("Return: "+data);
                if(data.indexOf("true") > -1) {
                    echoAlert("Password changed successfully.");
                    location.reload();
                }
                else echoAlert("Couldn't change password, password is incorrect.");
            });
        }
        else echoAlert("Passwords don't match. Check new password and retype exactly.");
    });

    // Submit extra account info
    $('#account-info-form').submit(function() {
        console.log("Account info submit");
        $.post("scripts/process-account-info.php",
        {
            facebook   : $('#facebook_link').val(),
            twitter    : $('#twitter_link').val(),
            instagram  : $('#instagram_link').val(),
            youtube    : $('#youtube_link').val(),
            pinterest   : $('#pinterest_link').val(),
            foundation : $('#foundation').val(),
            bio        : $('#bio').val()
        },
        function(data,status){
            console.log(data);
            if(data.indexOf("true") > -1) location.reload();
            else echoAlert("Couldn't save info, please refresh and try again.");
        });
    });

    //Add favourite action 
    $('#add-to-favourites').click(function() {
        console.log("Adding favourite");
        $.post("scripts/add-to-favourites.php",
        {
            product: $("#favourites-input").val()
        },
        function(data,status){
            console.log(data);
            $("#favourites-list").empty();
            loadFavourites();
        });
    })

    // Add product to favourites list
    $('#add-to-wishlist').click(function() {
        if( $("#shade-selector :selected").length ) var shade = $("#shade-selector :selected").val().split(",")[1];
        else var shade = "NULL";
        $.post("scripts/add-to-wishlist.php",
        {
            productID : $('#add-to-wishlist').attr('name'),
            productShade : shade
        },
        function(data,status){
            console.log(data);
            if(data.indexOf("true") > -1) $("#add-to-wishlist").addClass("btn-success");
        });
    });

    //Add-product-price button function
    $('#add-product-price').click(function() {
        if ($('#price-site-table').css('display') == 'none' ) {
            $('#price-site-table').show();
        }
        else {
            console.log("Price/site submit");
            $.post("scripts/add-price-site.php",
            {
                price    : $('#product-price').val(),
                site     : $('#product-site').val(),
                siteName : $('#product-site-name').val()
            },
            function(data,status){
                console.log("Data: "+data+"\nStatus: "+status);
                if(data == "true") echoAlert("Thank you for contributing to the beauty community!");
                else echoAlert("Couldn't add information. Pleae try again later.");
            });
        };
    });
    $('#price-site-select').change(function() {
        console.log("Site price changed");
        $('#product-price-current').empty();
        var priceData = $('#price-site-select').val();
        var siteData = priceData.split(",");
        $('#product-price-current').append( "<div style='text-align:center'><a class='price' href='"+siteData[1]+"'>"+siteData[0]+"</a><p>"+siteData[2]+"</p></div>" );
    });

    // Filter dupes
    $('#dupe-brand-filter-selector').submit(function() {
        console.log("Filter submit");
        var filters = new Array();
        $('.dupe-brand-filter').each(function() {
            if ($(this).is(':checked')) {
                console.log("Dupe brand filter "+$(this).val())
                filters.push($(this).val());
            };
        });
        var filterURL = filters.join(",");
        var url = "detail.php?id=" + $('#product-title').attr('name') + "&dupeBrands=" + filterURL + "#navigation";
        console.log("URL "+url);
        location.href = url;
    });

    //Upload comment data
    $('#author-review-form').submit(function(e) {
        e.preventDefault();
        var form = new FormData($('#author-review-form')[0]);
        $.ajax({
            url: "scripts/add-comment.php",
            method: "POST",
            data: form,
            processData: false,
            contentType: false,
            success: function(result){
                console.log("Response:\n\n"+result);
                $('#comments').empty();
                $('#comments').append(result);
            },
            error: function(er){
                console.log("Error\n"+er);
            }
        });
    });

    //Upload new post
    $('#newPostForm').submit(function(e) {
        e.preventDefault();
        console.log("Publishing post...\n\n");
        var form = new FormData($('#newPostForm')[0]);
        $.ajax({
            url: "scripts/new-post.php",
            method: "POST",
            data: form,
            processData: false,
            contentType: false,
            success: function(result){
                console.log(result);
            }
        });
    });

    //Upload new profile picture
    $('#profile-pic-upload-form').submit(function(e) {
        e.preventDefault();
        console.log('Profile pic upload\n\n');
        $("label[for='profilePicUpload']").removeClass("btn-danger");
        $("#profilePicUploadError").remove();
        var form = new FormData($('#profile-pic-upload-form')[0]);
        $.ajax({
            url: "scripts/add-profile-picture.php",
            method: "POST",
            data: form,
            processData: false,
            contentType: false,
            success: function(result){
                //console.log(result);
                if(result.indexOf("true") > -1) {
                    var pic_uri = result.split(",")[1];
                    $("#profilePicDisplay").attr("src",pic_uri);
                }
                else {
                    console.log("Adding failure class");
                    $("label[for='profilePicUpload']").addClass("btn-danger");
                    $("label[for='profilePicUpload']").append(" <i class='fa fa-times' id='profilePicUploadError'></i>");
                }
            }
        });
    });

    //Set shade img
    $('#shade-selector').change(function() {
        var val = $(this).val();
        var src = val.split(",");
        $('#shade-img').attr('src',src[0])

        //Empty current dupe list
        $('#dupe-complete-list').empty();
        $('#dupe-complete-list').remove();
        //Get shade from src array
        var shade = src[1];
        //Get product ID
        var id = $('#product-title').attr('name');
        //Log shade + ID for product
        console.log("ID: "+id+"\nShade: "+shade);
        //Get new dupe list based on shade
        $.post("scripts/get-dupes.php",
        {
            shade    : shade,
            id       : id
        },
        function(data,status){
            console.log("Data: "+data+"\nStatus: "+status);
            $('#comments-start').after(data);
        });
    });

    //Add class active to current page
    var page = getParameterByName('p');
    if (!page) page = 1;
    $('li#'+page).addClass('active');

    //Add class last to last comment
    $('.comment').last().addClass('last');

    //Watch for new post text changes i.e. # and @
    $('#newPost').on("keyup", function(){
        //Remove previous suggestions
        $("#atSuggestions").remove();
        $("#tagSuggestions").remove();
        //Get cursor position
        var cursorPos = $("#newPost").caret();
        //Define text, tag and positions
        var postText = $('#newPost').val();
        var tag = '';
        var at = '';
        var lastHashPos = -1;
        var lastAtPos = -1;
        var lastHashCharPos = -1;
        var lastAtCharPos = -1;

        //If any tag exists
        if( postText.indexOf('@') > -1 || postText.indexOf('#') > -1) {
            //Check every letter of string to get last tag position
            for(var i = 0; i < postText.length && i < cursorPos; i++) {
                if(postText.charAt(i) == "@") {
                    lastAtPos = i;
                }
                else if(postText.charAt(i) == "#") {
                    lastHashPos = i;
                }
            }
            //If @ position is higher (Editing @, not hashtag)
            if(lastAtPos > lastHashPos) {
                console.log("Editing @");
                //For the last @, check every proceeding alphaNum character to build tag
                for(var i = lastAtPos+1; i < postText.length; i++) {
                    console.log("Check char "+i);
                    if( isAlphaNum(postText.charAt(i)) ) {
                        tag += postText.charAt(i);
                    }
                    else {
                        lastAtCharPos = i;
                        break;
                    }
                }
                //Log last @ character position
                console.log("Last at char found at "+lastAtCharPos)
                //If the last @ character wasn't checked, last at character must be last global character i.e. total length
                if(lastAtCharPos == -1) {
                    lastAtCharPos = postText.length;
                }
                //If the cursor is after last @ character, @ tag not being edited. Break.
                if(cursorPos > lastAtCharPos) {
                    var stop = true;
                }
                if(stop !== true) {
                    //Log full tag text
                    console.log("at @"+tag);
                    //AJAX call for relevant @ tags
                    $.post("scripts/get-at-tag.php",
                    {
                        tag: tag,
                    },
                    //On receiving tag list
                    function(data,status){
                        //Parse and log JSON encoded tag list
                        var taglist = JSON.parse(data);
                        console.log(taglist);
                        //If data exists
                        if(data) {
                            //Log success and append list of tags after text box
                            console.log("At tags found:\n");
                            append = "<ul class='list-group' id='atSuggestions' style='position: absolute; width: 95%; z-index: 10;'>"
                            for(var i=0; i<taglist.length; i++) {
                                append += "<li class='atSuggestion list-group-item' value='@"+taglist[i]+"'><a>"+taglist[i]+"</a></li>";
                            }
                            append += "</ul>";
                            $("#newPost").after(append);
                            //Begin an observer for suggestion click
                            atTagObserver();
                        }
                        //If no data, log no data found
                        else {
                            console.log("No at tags found");
                        }
                    });
                }
            }
            //If @ is lower than # position (Editing #, not @)
            else {
                //For the last #, check every proceeding alphaNum character to build tag
                for(var i = lastHashPos+1; i < postText.length; i++) {
                    if( isAlphaNum(postText.charAt(i)) ) {
                        tag += postText.charAt(i);
                    }
                    else {
                        lastHashCharPos = i;
                        break;
                    }
                }
                //If last # character wasn't found, must be last global character i.e. post length
                if(lastHashCharPos == -1) {
                    lastHashCharPos = postText.length;
                }
                //Log last # character position
                console.log("Last hashtag char at "+lastHashCharPos);
                //If cursor is after last # character position, # is not being edited
                if(cursorPos > lastHashCharPos) {
                    var stop = true;
                }
                if(stop !== true) {
                    //Log full tag text
                    console.log("Hashtag #"+tag);
                    //AJAX call for relevant # tags
                    $.post("scripts/get-trending-hashtag.php",
                    {
                        tag: tag,
                    },
                    //On receiving tag list
                    function(data,status){
                        //Parse and LOG JSON encoded tag list
                        var taglist = JSON.parse(data);
                        console.log(taglist);
                        //If dta exists
                        if(data) {
                            //Log success and append tag list after textbox
                            console.log("Hashtags found:\n");
                            append = "<ul class='list-group' id='tagSuggestions' style='position: absolute; width: 95%; z-index: 10;'>"
                            for(var i=0; i<taglist.length; i++) {
                                append += "<li class='tagSuggestion list-group-item' value='"+taglist[i]+"'><a>"+taglist[i]+"</a></li>";
                            }
                            append += "</ul>";
                            $("#newPost").after(append);
                            //Begin an observer for suggestion click
                            hashtagObserver();
                        }
                        //If no data, log no data found
                        else {
                            console.log("No hashtags found");
                        }
                    });
                }
            }
        }
    });
});


var page;
if(document.location.pathname.match(/[^\/]+$/)) page = document.location.pathname.match(/[^\/]+$/)[0];
else page='index.php';

//If posts exist add load more button 
if($('.following-post').length > 0) {
    $("#following-post-area").after(
        "<div class='text-center'>\
            <img src='/BeautyHub/img/ajax-loader.svg' style='width: 5em;' id='postLoading'>\
        </div>");
    $("#postLoading").hide();
};

//Load more posts for social feed
//Add infinite scroll loading for social feed
if(page == "social.php") {
    $(window).scroll(function(){
        if ($(document).height() - $(window).height() == $(window).scrollTop()
        && $("#noMorePosts").length < 1) {
            //Show loading icon
            $("#postLoading").show();
            //Set offset
            var offset = $('.following-post').length;
            console.log(offset+"\n");
            //Post offset to load further posts
            $.ajax({
                url: "scripts/load-more-posts.php",
                method: "POST",
                data: {
                    offset: offset,
                },
                async: false,
                success: function(data){
                    console.log(data);
                    //If has data append data
                    if(data != " "
                    && data != ""
                    && data != null) {
                        $("#following-post-area").append(data);
                    }
                    //Hide loading icon
                    $("#postLoading").hide();
                    myImgObserver();
                }
            });
        }
    });
};

//Set rating funtion
function ratingStars() 
{
    $(".fa-star-o")
        .mouseover(function() {
            var num = $(this).attr('id')[$(this).attr('id').length -1];
            for(var i=0;i<=num;i++) {
                $("#star"+i).removeClass("fa-star-o");
                $("#star"+i).addClass("fa-star");
            }
        })
        .mouseout(function() {
            for(var i=0;i<6;i++){
                if(i>$('#productRating').val()) {
                    $("#star"+i).removeClass("fa-star");
                    $("#star"+i).addClass("fa-star-o");
                }
                else if(i>$('#authorRating').val()) {
                        $("#star"+i).removeClass("fa-star");
                        $("#star"+i).addClass("fa-star-o");
                    }
            }
        })
        .click(function() {
            var num = $(this).attr('id')[$(this).attr('id').length -1];
            $('#productRating').val(num);
            $('#authorRating').val(num);
        });
};

// Close modal when needed
function modalObserver() 
{
    $("#myModal").click(function() {
        console.log("Closing image");
        $("#myModal").remove();
    });
};

// Set comment form handler
function commentFormHandler()
{
    $('#author-review-form').submit(function(e) {
        e.preventDefault();
        var form = new FormData($('#author-review-form')[0]);
        $.ajax({
            url: "scripts/add-comment.php",
            method: "POST",
            data: form,
            processData: false,
            contentType: false,
            success: function(result){
                console.log("Response:\n\n"+result);
                $('#comments').empty();
                $('#comments').append(result);
            },
            error: function(er){
                console.log("Error\n"+er);
            }
        });
    });
};

// Logout user
function logout()
{
    $.post("scripts/process-logout.php",
    {},
    function(data,status){
        if(status=="success") location.reload();
    });
};

// Remove item from wishlist
function removeWishlist(data)
{
    var dataArray = data.split(",");
    var id = dataArray[0];
    var shade = dataArray[1];
    var user = dataArray[2];
    $.post("scripts/remove-from-wishlist.php",
    {
        id: id,
        shade: shade
    },
    function(data,status) {
        console.log("Remove returned: "+data);
        if(data.indexOf('true') > -1) {
            $('#wishlist-product-row').remove();
            printWishlist(user);
        }
    });
};

//Remove item from favourites
function removeFromFav(data) {
    $.post("scripts/remove-from-favourites.php",
    {
        data: data
    },
    function(data,status) {
        console.log(data);
        if(data.indexOf("true") > -1) {
            $("#favourites-list").empty();
            loadFavourites();
        }
    })
};

//Load favourites list 
function loadFavourites() {
    $.get("scripts/load-favourites.php",
    function(data,status){
        $("#favourites-list").append(data);
    });
};

//Print wishlist info
function printWishlist(user)
{
    $.post("scripts/load-wishlist-products.php",
    {
        user: user
    },
    function(data,status){
        $('.col-md-9 > .box').after(data);
    });
};

// Get GET vars from URL
function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
};

// Scrape product info
function scrapeProduct(search) {
    console.log("Scraping "+search);
    loadingAJAX();
    var productInfo =
        $.ajax({
            type: "GET",
            url: "scripts/scrape-product.php?search="+search,
            data: search,
            success: 
                function(data,status){
                    //console.log(data);
                    doneAJAX();
                    return data;
                },
            async: false
        });
    return productInfo.responseText
};

// Dupe form remover
function dupeFormRemover() {
    $("body").click(function(e) {
        if( $("#new-dupe-enter").css("display") === "block" ) {
            if( e.target.id == "new-dupe-enter") {
                $("#new-dupe-enter").hide();
            }
        }
    });
}

// Follow user
function followProfile(id) {
    console.log("Follow profile.\nID: "+id);
    $.post("scripts/follow-user.php",
    {
        id: id,
    },
    function(data,status){
        console.log(data);
        if(data.indexOf("true") > -1) {
            $("button#notFollowing").remove();
            $("strong#profileUsername").after(
                "<button type='button' class='btn btn-success follow-button' id='unfollow' onclick='unfollowProfile(\" "+ id + "\")'> \
                Unfollow&nbsp;<i class='fa fa-minus'></i> \
            </button>"
            );
        }
    });
};

// Unfollow user
function unfollowProfile(id) {
    console.log("Unfollow profile.\nID: "+id);
    $.post("scripts/unfollow-user.php",
    {
        id: id,
    },
    function(data,status){
        console.log(data);
        if(data.indexOf("true") > -1) {
            $("button#unfollow").remove();
            $("strong#profileUsername").after(
                "<button type='button' class='btn btn-primary follow-button' id='notFollowing' onclick='followProfile(\"" + id + "\")'> \
                    Follow&nbsp;<i class='fa fa-plus'></i> \
                </button>"
            );
        }
    });
};

// Report user
function reportProfile(id) {
    console.log("Report profile "+id);
    $.post("scripts/report-user.php",
    {
        id: id,
    },
    function(data,status){
        console.log(data);
        if(data.indexOf("true") > -1) {
            $("i#reportUser").removeClass("fa-exclamation");
            $("i#reportUser").addClass("fa-check");
        }
    });
};

//Display image lightbox
function myImgObserver() {
    $(".myImg").click(function() {
        console.log("Image tapped. Source " + $(this).attr("name"));
        $('#top').before(" \
            <div id='myModal'> \
                <img id='myModalImg' src='"+ $(this).attr("name") +"'> \
            </div>\
        ");
        $("#myModal").show();
        modalObserver();
    });
};

// Display echoAlert message
function echoAlert(message) {
    $("div#content").prepend(" \
    <div class='container' id='AJAXalert'> \
        <div class='col-lg-12 col-sm-12 col-md-12'> \
            <div class='box'>" + 
                "<i class='fa fa-exclamation-circle'></i>&nbsp;" + message + " \
            </div> \
        </div> \
    </div>");
};

//Add selcted tag
function atTagObserver() {
    $(".atSuggestion").click(function(){
        var cursorPos = $("#newPost").caret();
        var atTag = $(this).attr('value');
        var postText = $('#newPost').val();
        var lastPos = postText.length;
        var tag = "@";
        var lastAtPos = -1;
        //Check every letter of string from start to cursor to get last @ position
        for(var i = 0; i < lastPos && i < cursorPos; i++) {
            if(postText.charAt(i) == "@") {
                lastAtPos = i;
            }
        }
        //For the last @, check every proceeding alphaNum character to build tag
        for(var i = lastAtPos+1; i < lastPos; i++) {
            if( isAlphaNum(postText.charAt(i)) ) {
                tag += postText.charAt(i);
            }
            else {
                break;
            }
        }
        /**
         * New textbox value is; 
         * current text from beginning to the @ tag, 
         * plus the new @ tag,
         * space, 
         * original text from the cursor position to the end of the original
         */
        var newText = postText.substring(0,lastAtPos)+atTag+" "+postText.substring(cursorPos,lastPos);
        //Log the new text
        console.log("New Text\n"+newText);
        //Insert the new text and remove suggestion box
        $('#newPost').val(newText);
        //Move mouse to end of @
        $('#newPost').caret(lastHashPos+hashtag.length);
        $("#atSuggestions").remove();
    });
};
function hashtagObserver() {
    $(".tagSuggestion").click(function(){
        var cursorPos = $("#newPost").caret();
        var hashtag = $(this).attr('value');
        var postText = $('#newPost').val();
        var lastPos = postText.length;
        var tag = "#";
        var lastHashPos = -1;
        //Check every letter of string from start to cursor to get last # position
        for(var i = 0; i < lastPos && i < cursorPos; i++) {
            if(postText.charAt(i) == "#") {
                lastHashPos = i;
            }
        }
        //For the last #, check every proceeding alphaNum character to build tag
        for(var i = lastHashPos+1; i < lastPos; i++) {
            if( isAlphaNum(postText.charAt(i)) ) {
                tag += postText.charAt(i);
            }
            else {
                break;
            }
        }
        /**
         * New textbox value is; 
         * current text from beginning to the # tag, 
         * plus the new # tag,
         * space, 
         * original text from the cursor position to the end of the original
         */
        var newText = postText.substring(0,lastHashPos)+hashtag+" "+postText.substring(cursorPos,lastPos);
        //Log new text
        console.log("New Text\n"+newText);
        //Insert the new text and remove suggestion box
        $('#newPost').val(newText);
        //Move mouse to end of #
        $('#newPost').caret(lastHashPos+hashtag.length);
        $("#tagSuggestions").remove();
    });
};

//Reload page
function pageReload() {
    location.reload();
}

//Check string is alhpanumeric
function isAlphaNum(string) {
    if( string.match(/^[a-zA-Z0-9]+/) ) {
        return true
   }
   else {
       return false;
   }
};

/*
// Set loader for AJAX
function loadingAJAX() { $('div#top').before("<div id='loader' style='width:100%;height:100%;position:fixed;background:rgba(0,0,0,.5) url(img/ajax-loader.svg) center center no-repeat;z-index:16;'></div>"); }
function doneAJAX() { $("div#loader").remove(); }

// Set AJAX global handlers
$(document).ajaxSend(function() {
    $("#AJAXalert").remove();
    loadingAJAX();
});
$(document).ajaxComplete(function() {
    doneAJAX();
});*/