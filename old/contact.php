<?php
    require_once 'scripts/functions.php';
    session_start();
    loadHead();
    loadTopBar();
    loadNavBar();
    beginContent();
?>

                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li><a href="#" class="nav-link">Home</a>
                        </li>
                        <li>Contact</li>
                    </ul>

                </div>

                <div class="col-md-3">
                    <!-- *** PAGES MENU ***
 _________________________________________________________ -->
                    <div class="panel panel-default sidebar-menu">

                        <div class="panel-heading">
                            <h3 class="panel-title">Pages</h3>
                        </div>

                        <div class="panel-body">
                            <ul class="nav nav-pills nav-stacked">
                                <li>
                                    <a href="contact.php">Contact page</a>
                                </li>
                                <li>
                                    <a href="about.php">About BeautyHub</a>
                                </li>

                            </ul>

                        </div>
                    </div>

                    <!-- *** PAGES MENU END *** -->


                    <div class="banner">
                        <a href="#">
                            <img src="img/banner.jpg" alt="sales 2014" class="img-responsive">
                        </a>
                    </div>
                </div>

                <div class="col-md-9">


                    <div class="box" id="contact">
                        <h1>Contact</h1>

                        <p class="lead">If you've encountered a problem, you have a new function you'd like implemented, or you have a business idea you'd like to discuss with BeautyHub, contact us here!</p>
                        <p>Please feel free to contact us, our customer service center is working for you 24/7. Please leave 1 business day to receive a detailed reply.</p>

                        <hr>

                        <div class="row">
                            <!-- <div class="col-sm-4">
                                <h3><i class="fa fa-map-marker"></i> Address</h3>
                                <p>13/25 New Avenue
                                    <br>New Heaven
                                    <br>45Y 73J
                                    <br>England
                                    <br>
                                    <strong>Great Britain</strong>
                                </p>
                            </div>
                                /.col-sm-4 -->
                            <!-- <div class="col-sm-4">
                                <h3><i class="fa fa-phone"></i> Call center</h3>
                                <p class="text-muted">This number is toll free if calling from Great Britain otherwise we advise you to use the electronic form of communication.</p>
                                <p><strong>+33 555 444 333</strong>
                                </p>
                            </div>
                                /.col-sm-4 -->
                            <div class="col-sm-12">
                                <h3><i class="fa fa-envelope"></i> Electronic support</h3>
                                <p class="text-muted">Please feel free to write an email to us or to use our electronic ticketing system.</p>
                                <ul>
                                    <li>
                                        <strong>
                                            <a href="mailto:beautyhub@nygmarosebeauty.com">beautyhub@nygmarosebeauty.com</a>
                                        </strong>
                                    </li>
                                </ul>
                            </div>
                            <!-- /.col-sm-12 -->
                        </div>
                        <!-- /.row -->
                        <hr>
                        <h2>Contact form</h2>

                        <form id="contact-form" action="javascript:void(0)">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="firstname">Firstname*</label>
                                        <input type="text" class="form-control" id="firstname">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="lastname">Lastname</label>
                                        <input type="text" class="form-control" id="lastname">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="email">Email*</label>
                                        <input type="text" class="form-control" id="email">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="subject">Subject*</label>
                                        <input type="text" class="form-control" id="subject">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="message">Message*</label>
                                        <textarea id="message" class="form-control"></textarea>
                                    </div>
                                </div>

                                <div class="col-sm-12 text-center">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-envelope-o"></i> Send message
                                    </button>
                                </div>
                            </div>
                            <!-- /.row -->
                        </form>


                    </div>


                </div>
                <!-- /.col-md-9 -->
<?php loadFoot(); ?>
