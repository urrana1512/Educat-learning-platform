    <?php
        $currentURL = $_SERVER['PHP_SELF'];
        $currentPage = basename($currentURL);
    ?>
    <header style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border-bottom: 1px solid #E5E7EB; position: sticky; top: 0; z-index: 100;">
        <nav style="max-width: 1200px; margin: 0 auto; padding: 15px 20px; display: flex; align-items: center; justify-content: space-between; gap: 20px;">
            

            <!-- Logo -->
            <div style="flex-shrink: 0; display: flex; align-items: center;height: 50px;">
                <a href="index.php" style="display: flex; align-items: center; ">
                    <img src="assets/img/EduCat (3).png" alt="EduCat Logo" style="height: 120px; width: auto; object-fit: contain;">
                </a>
            </div>


            <!-- Search Bar -->
            <div style="flex-grow: 1; max-width: 500px; margin: 0 20px;" class="ta-header-search-container">
                <form method="GET" action="search.php" style="position: relative; width: 100%;">
                    <svg width="18" height="18" fill="none" stroke="#9CA3AF" stroke-width="2" viewBox="0 0 24 24" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" placeholder="What do you want to learn?" name="search_term" 
                           style="width: 100%; padding: 12px 14px 12px 40px; border: 1px solid #D1D5DB; border-radius: 99px; font-family: 'Inter', sans-serif; font-size: 0.95rem; color: #111827; outline: none; transition: border-color 0.2s ease, box-shadow 0.2s ease; background: #F9FAFB;"
                           onfocus="this.style.borderColor='#3B82F6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)';" onblur="this.style.borderColor='#D1D5DB'; this.style.boxShadow='none';"
                           <?php if(isset($_GET["search_term"]) && $currentPage == "search.php"){echo 'value="' . htmlspecialchars($_GET["search_term"]) . '"';}?>>
                </form>
            </div>

            <!-- Mobile Menu Toggle Button (Visible only on mobile via CSS) -->
            <button class="ta-menu-btn" style="display: none; background: transparent; border: none; cursor: pointer; color: #4B5563;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>

            <!-- Navigation Links -->
            <div class="ta-desktop-menu" style="display: flex; align-items: center; gap: 24px;">
                <?php if(isset($_SESSION['educat_logedin_user_id'])): ?>
                    <?php if($user_role == 3): ?>
                        <a href="create-instructor.php" style="color: #4B5563; font-family: 'Inter', sans-serif; font-size: 0.95rem; font-weight: 500; text-decoration: none; transition: color 0.2s ease;" onmouseover="this.style.color='#2563EB';" onmouseout="this.style.color='#4B5563';">Teach</a>
                    <?php elseif ($user_role == 2): ?>
                        <a href="instructor/" style="color: #4B5563; font-family: 'Inter', sans-serif; font-size: 0.95rem; font-weight: 500; text-decoration: none; transition: color 0.2s ease;" onmouseover="this.style.color='#2563EB';" onmouseout="this.style.color='#4B5563';">Instructor Panel</a>
                    <?php elseif ($user_role == 1): ?>
                        <a href="dashboard/" style="color: #4B5563; font-family: 'Inter', sans-serif; font-size: 0.95rem; font-weight: 500; text-decoration: none; transition: color 0.2s ease;" onmouseover="this.style.color='#2563EB';" onmouseout="this.style.color='#4B5563';">Admin Panel</a>
                    <?php endif; ?>
                    
                    <a href="student/index.php" style="color: #4B5563; font-family: 'Inter', sans-serif; font-size: 0.95rem; font-weight: 500; text-decoration: none; transition: color 0.2s ease; outline: none; box-shadow: none;">My Learning</a>
                    
                    <a href="myaccount.php" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; background: #EFF6FF; border: 1px solid #BFDBFE; color: #2563EB; transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 6px -1px rgba(37, 99, 235, 0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </a>
                <?php else: ?>
                    <a href="sign-in.php" style="color: #4B5563; font-family: 'Inter', sans-serif; font-size: 0.95rem; font-weight: 600; text-decoration: none; transition: color 0.2s ease;" onmouseover="this.style.color='#111827';" onmouseout="this.style.color='#4B5563';">Log in</a>
                    <a href="sign-up.php" style="background: #2563EB; color: #fff; font-family: 'Inter', sans-serif; font-size: 0.95rem; font-weight: 600; text-decoration: none; padding: 10px 20px; border-radius: 8px; transition: background 0.2s ease, box-shadow 0.2s ease;" onmouseover="this.style.background='#1D4ED8'; this.style.boxShadow='0 4px 6px -1px rgba(37, 99, 235, 0.3)';" onmouseout="this.style.background='#2563EB'; this.style.boxShadow='none';">Sign up</a>
                <?php endif; ?>
            </div>
            
        </nav>
    </header>