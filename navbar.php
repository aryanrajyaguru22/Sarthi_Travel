<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    nav {
        background-color: #2d89ef;
        color: white;
        font-family: 'Segoe UI', sans-serif;
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .nav-left a, .nav-right a {
        color: white;
        text-decoration: none;
        margin: 8px 12px;
        font-weight: 600;
        transition: color 0.3s, transform 0.3s;
    }

    .nav-left a:hover, .nav-right a:hover {
        color: #ffd700;
        transform: scale(1.05);
    }

    .nav-left, .nav-right {
        display: flex;
        align-items: center;
    }

    .menu-toggle {
        display: none;
        flex-direction: column;
        cursor: pointer;
    }

    .menu-toggle div {
        width: 25px;
        height: 3px;
        background-color: white;
        margin: 4px 0;
        transition: 0.4s;
    }

    @media (max-width: 768px) {
        .nav-left {
            flex-direction: column;
            width: 100%;
            display: none;
            margin-top: 10px;
        }

        .nav-left a {
            margin: 10px 0;
        }

        .nav-left.show {
            display: flex;
        }

        .menu-toggle {
            display: flex;
        }
    }
</style>

<nav>
    <div style="font-size: 1.5rem; font-weight: bold;">
        🚍 સારથી ટ્રાવેલ્સ
    </div>

    <div class="menu-toggle" onclick="toggleMenu()">
        <div></div>
        <div></div>
        <div></div>
    </div>

    <div class="nav-left" id="navLinks">
        <a href="dashboard.php">ડેશબોર્ડ</a>
        <a href="add_bus.php">બસ મેનેજ</a>
        <a href="trip.php">ટ્રિપ ઉમેરો</a>
        <a href="trip_manage.php">ટ્રિપ મેનેજ</a>
        <a href="meal_items.php">ભોજનનું સંચાલન</a>
        <a href="ingredients.php">વસ્તુ સામગ્રી</a>
    </div>

    <div class="nav-right">
        <a href="logout.php" style="background: white; color: #2d89ef; padding: 6px 12px; border-radius: 5px;">Logout</a>
    </div>
</nav>

<script>
    function toggleMenu() {
        const navLinks = document.getElementById("navLinks");
        navLinks.classList.toggle("show");
    }
</script>
