    <!-- Footer Begin -->
    <footer class="footer">
        <div class="container">
            <div class="footer__top">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="footer__top__logo">
                            <h2 class="site-title-footer">
                                <a href="/projetttwebbbbbbbbb/index.php?controller=offre&action=liste">
                                    <span class="letter-a">A</span>
                                    <span class="letter-b">b</span>
                                    <span class="letter-l">l</span>
                                    <span class="letter-e">e</span>
                                    <span class="letter-link">Link</span>
                                </a>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer__option">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <div class="footer__option__item">
                            <h5>À propos d'AbleLink</h5>
                            <p>AbleLink connecte les chercheurs d'emploi en situation de handicap aux entreprises inclusives.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <div class="footer__option__item">
                            <h5>Ressources</h5>
                            <ul>
                                <li><a href="/projetttwebbbbbbbbb/index.php?controller=offre&action=liste">Offres d'emploi</a></li>
                                <li><a href="/projetttwebbbbbbbbb/index.php?controller=offre&action=gestion">Gestion</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer__copyright">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <p>
                            Copyright © <?= date('Y') ?> AbleLink | Vers un avenir professionnel inclusif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer End -->

    <!-- Js Plugins -->
    <script src="/projetttwebbbbbbbbb/assets/js/jquery-3.3.1.min.js"></script>
    <script src="/projetttwebbbbbbbbb/assets/js/bootstrap.min.js"></script>
    <script src="/projetttwebbbbbbbbb/assets/js/jquery.magnific-popup.min.js"></script>
    <script src="/projetttwebbbbbbbbb/assets/js/mixitup.min.js"></script>
    <script src="/projetttwebbbbbbbbb/assets/js/masonry.pkgd.min.js"></script>
    <script src="/projetttwebbbbbbbbb/assets/js/jquery.slicknav.js"></script>
    <script src="/projetttwebbbbbbbbb/assets/js/owl.carousel.min.js"></script>
    <script src="/projetttwebbbbbbbbb/assets/js/main.js"></script>

    <!-- Smooth Scroll for Hero Buttons -->
    <script>
        $(document).ready(function() {
            // Smooth scroll for hero buttons
            $('.hero__btn[href^="#"]').on('click', function(e) {
                var target = $(this.getAttribute('href'));
                if(target.length) {
                    e.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 100
                    }, 1000);
                }
            });
        });
    </script>

</body>
</html>