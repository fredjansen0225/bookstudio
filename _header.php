        <div id="header">
            <div class="bg-help">

                <?php if(isset($_SESSION['client_id'])) { ?>
                    <div style="color: white; float: right; margin: 20px 20px 0px 10px;">
                       <span style="color: #8fc300;"><?php echo $_SESSION['client_name']; ?> </span> <a href="logout.php" style="color: white">Log Out</a>
                    </div>
                <?php } ?>

                <div class="inBox">
                    <h1 id="logo"><a href='#'>3rdStreetAdr Mixing Studio</a></h1>
                    <p id="claim">Studio Booking Calendar</p>
                    <hr class="hidden" />
                </div>

            </div>
        </div>
        <div class="shadow"></div>
        <div class="hideSkipLink">
        </div>
