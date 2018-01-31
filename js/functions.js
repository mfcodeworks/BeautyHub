$(document).ready(function() {
    // Append active to current nav link
    var page;
    if(document.location.pathname.match(/[^\/]+$/)) page = document.location.pathname.match(/[^\/]+$/)[0];
    else page='index.php';
    console.log(page);
    $("li[name='"+page+"']").addClass('active');

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
    }
    // Hide form until clicked
    $("#new-shade-enter").hide();

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
        })
        $("#new-shade-enter").hide();
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
    if(page == "detail.php") setTimeout(addProductView, 10000);
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
                if(data == "true") {
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
            pintrest   : $('#pintrest_link').val(),
            foundation : $('#foundation').val(),
            favourites : $('#favourites').val(),
            bio        : $('#bio').val()
        },
        function(data,status){
            console.log("Data "+data+"\nStatus "+status);
            if(data == "true") location.reload();
            else echoAlert("Couldn't save info, please refresh and try again.");
        });
    });

    // Add product to favourites list
    $('#add-to-wishlist').click(function() {
        if( $("#shade-selector :selected").val().length ) var shade = $("#shade-selector :selected").val().split(",")[1];
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

    //Upload new profile picture
    $('#profile-pic-upload-form').submit(function(e) {
        e.preventDefault();
        console.log('Profile pic upload\n\n');
        var form = new FormData($('#profile-pic-upload-form')[0]);
        $.ajax({
            url: "scripts/add-profile-picture.php",
            method: "POST",
            data: form,
            processData: false,
            contentType: false,
            success: function(result){
                console.log(result);
                //location.reload();
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
});

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
        if(data.indexOf('1') > -1) {
            $('#wishlist-product-row').remove();
            printWishlist(user);
        }
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
});