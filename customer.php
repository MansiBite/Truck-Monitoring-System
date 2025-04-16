<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="css/customer.css">
    <script>
        // JavaScript for Sidebar Menu
        document.addEventListener("DOMContentLoaded", () => {
            const menuButtons = document.querySelectorAll(".menu-btn");

            menuButtons.forEach(button => {
                button.addEventListener("click", () => {
                    const submenu = button.nextElementSibling;
                    submenu.style.display = submenu.style.display === "block" ? "none" : "block";
                });
            });
        });
    </script>
</head>

<body>
    <div class="navbar">
        <h1>Customer Dashboard</h1>
        <div style="display: flex; gap: 10px;">
            <a class="new-transporter-btn" href="tranporterlogin.html">Create New Transporter</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Menu</h2>
            <ul class="menu">
                <li>
                    <button class="menu-btn">Loading Programme</button>
                    <ul class="submenu">
                        <li><a href="include/loading_pdisplay.php">Display</a></li>
                    </ul>
                </li>
                <li>
                    <button class="menu-btn">Truck Requirement Indenting</button>
                    <ul class="submenu">
                        <li><a href="include/truckindenting.php">Create</a></li>
                        <li><a href="include/truckindentingedit.php">Edit</a></li>
                        <li><a href="include/truckindentingdisplay.php">Display</a></li>
                    </ul>
                </li>
                <li>
                    <button class="menu-btn">Blacklisted Drivers</button>
                    <ul class="submenu">
                        <li><a href="include/blacklistdriverdisplay.php">Display</a></li>
                    </ul>
                </li>
                <li>
                    <button class="menu-btn">Blacklisted Trucks</button>
                    <ul class="submenu">
                        <li><a href="include/blacklisttruckdisplay.php">Display</a></li>
                    </ul>
                </li>
                <li>
                    <button class="menu-btn">Blacklisted Transporters</button>
                    <ul class="submenu">
                        <li><a href="include/blacklistTransporterdisplay.php">Display</a></li>
                    </ul>
                </li>
                <li>
                    <button class="menu-btn">Provisional Truck No.</button>
                    <ul class="submenu">
                        <li><a href="include/provisional_truck.php">Create</a></li>
                        <li><a href="include/provisional_truck_edit.php">Edit</a></li>
                        <li><a href="include/provisional_truck_display.php">Display</a></li>
                    </ul>
                </li>
                <li>
                    <button class="menu-btn">Status @WCL Check Post</button>
                    <ul class="submenu">

                        <li><a href="include/status@wcl_display.php">Display</a></li>
                    </ul>
                </li>
                <li>
                    <button class="menu-btn">Sales Order Details</button>
                    <ul class="submenu">
                        <li><a href="?page=display-sales-order">Display</a></li>
                    </ul>
                </li>
                <li>
                    <button class="menu-btn">Lifting Details</button>
                    <ul class="submenu">
                        <li><a href="?page=display-lifting">Display</a></li>
                    </ul>
                </li>
                <li>
                    <button class="menu-btn">Invoice Details</button>
                    <ul class="submenu">
                        <li><a href="?page=display-invoice">Display</a></li>
                    </ul>
                </li>
                <li>
                    <button class="menu-btn">Refund Details</button>
                    <ul class="submenu">
                        <li><a href="?page=display-refund">Display</a></li>
                    </ul>
                </li>
                <button class="create-transporter-btn">
                    <a href="savephp/transporterdetails.php" style="color: white; text-decoration: none;">Created
                        Transporter</a>
                </button>
            </ul>
        </div>

        <div class="main-content">
            <!-- Carousel Slider Container -->
            <div class="container mt-4">
                <div id="carouselExampleDark" class="carousel carousel-dark slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="0" class="active"
                            aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="1"
                            aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="2"
                            aria-label="Slide 3"></button>
                    </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active" data-bs-interval="10000">
                            <img src="images/Truk12.jpg" class="d-block w-100" alt="...">
                        </div>
                        <div class="carousel-item" data-bs-interval="2000">
                            <img src="images/13.jpg" 3 class="d-block w-100" alt="...">
                        </div>
                        <div class="carousel-item">
                            <img src="images/12.jpg" class="d-block w-100" alt="...">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>