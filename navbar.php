<!-- navbar.php -->

<!-- navbar.php -->
<style>
    nav {
        background-color: #2d89ef;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
        font-family: Arial, sans-serif;
    }
    nav a {
        color: white;
        text-decoration: none;
        margin: 0 12px;
        font-weight: bold;
    }
    nav a:hover {
        text-decoration: underline;
    }
    .nav-left, .nav-right {
        display: flex;
        align-items: center;
    }
</style>

<nav>
    <div class="nav-left">
        <a href="dashboard.php">Dashboard</a>
        <a href="add_bus.php">Bus Manage</a>
        <a href="trip.php">Add Trip</a>
        <a href="trip_manage.php">Trip Manage</a>
        <a href="meal_items.php">Manage Meal</a>
        <a href="ingredients.php">Manage Ingredients</a>
    </div>
    <div class="nav-right">
        <a href="logout.php">Logout</a>
    </div>
</nav>
