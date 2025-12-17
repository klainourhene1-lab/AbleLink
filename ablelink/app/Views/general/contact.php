<?php
$message_sent = isset($message_sent) ? $message_sent : false;
?>
    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="/projetweb/ablelink/"><i class="fa fa-home"></i> Accueil</a>
                        <span>Contact</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="contact spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="section-title">
                        <span>Contact</span>
                        <h2>Entrer en Contact</h2>
                    </div>
                    <p>Nous répondons aux questions sur l'inclusion, l'emploi et nos services.</p>
                    <ul>
                        <li><i class="fa fa-envelope"></i> contact@ablelink.org</li>
                        <li><i class="fa fa-phone"></i> +216 00 000 000</li>
                        <li><i class="fa fa-map-marker"></i> Tunis, Tunisie</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <?php if ($message_sent): ?>
                        <div style="background: rgba(16,185,129,0.15); border: 1px solid #10b981; color: #10b981; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                            ✅ Votre message a été envoyé avec succès!
                        </div>
                    <?php endif; ?>
                    <form class="contact__form" method="POST">
                        <div class="row">
                            <div class="col-lg-6">
                                <input type="text" name="name" placeholder="Votre nom" required class="form-control" style="margin-bottom:10px;">
                            </div>
                            <div class="col-lg-6">
                                <input type="email" name="email" placeholder="Votre email" required class="form-control" style="margin-bottom:10px;">
                            </div>
                            <div class="col-lg-12">
                                <input type="text" name="subject" placeholder="Sujet" required class="form-control" style="margin-bottom:10px;">
                            </div>
                            <div class="col-lg-12">
                                <textarea name="message" placeholder="Message" rows="5" required class="form-control" style="margin-bottom:10px;"></textarea>
                            </div>
                            <div class="col-lg-12">
                                <button type="submit" class="site-btn">Envoyer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    

