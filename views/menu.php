<div class="menu">
<nav class="navbar">
    <?php
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if(isset($_SESSION['cahol_nev'])): ?>
            <a href="auto_tipus_lista.php">Auto típusok</a>
            <?php if($_SESSION['cahol_jogosultsag'] == "admin"): ?>
                <a href="uj_auto_tipus.php">Új autó típus</a>
            <?php endif; ?>

            <a href="autok_lista.php">Céges autók</a>
            <?php if($_SESSION['cahol_jogosultsag'] == "admin"): ?>
                <a href="uj_auto.php">Új autó</a>
            <?php endif; ?>
            <?php if($_SESSION['cahol_jogosultsag'] != "admin"): ?>
                <a href="kiadva_lista.php">Hozzám rendelt autó(k)</a>
            <?php endif; ?>
            <a href="osztalyok_lista.php">Osztályok</a>
            <a href="telephelyek_lista.php">Telephelyek</a>
            <a href="kijelentkez.php" style="color: grey;">Kijelentkezés</a>
        <?php else: ?>
            <a href="bejelentkezes.php">Bejelentkezés</a>
            <a href="regisztracio.php">Regisztráció</a>
        <?php endif;
    ?>
</nav>
    <?php
        if(isset($_SESSION['cahol_nev']) && isset($_SESSION['cahol_dolg_id'])): ?>
            <div class="profile"><a href="profilom.php"><?php echo $_SESSION['cahol_nev'] ?></a></div>

    <?php endif;?>
</div>