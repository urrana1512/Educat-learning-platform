<?php
/**
 * instructor/includes/footer.php
 * Standard footer scripts — included at the bottom of every instructor page.
 */
?>
    <script src="../dashboard/assets/js/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js"></script>
    <script src="../dashboard/assets/js/bootstrap.bundle.min.js"></script>
    <script>
    // Instructor Sidebar JS — inlined for reliability
    (function($){
        $(document).ready(function(){

            // Feather if available
            if(typeof feather !== 'undefined') feather.replace();

            // Mobile sidebar toggle
            var $sidebar = $('#ins-sidebar');
            var $overlay = $('#ins-overlay');

            $('#ins-mobile-toggle').on('click', function(){
                $sidebar.toggleClass('open');
                $overlay.toggleClass('show');
            });

            $overlay.on('click', function(){
                $sidebar.removeClass('open');
                $overlay.removeClass('show');
            });

            // Desktop sidebar collapse
            $('#ins-toggle').on('click', function(){
                $('body').toggleClass('ins-collapsed');
                localStorage.setItem('ins-sidebar', $('body').hasClass('ins-collapsed') ? '1' : '0');
            });

            if(localStorage.getItem('ins-sidebar') === '1'){
                $('body').addClass('ins-collapsed');
            }

            // Profile dropdown
            $('#ins-profile-btn').on('click', function(e){
                e.stopPropagation();
                $('#ins-profile-menu').toggleClass('show');
            });

            $(document).on('click', function(){
                $('#ins-profile-menu').removeClass('show');
            });

            // Sidebar submenu
            $(document).on('click', '.ins-submenu-toggle', function(e){
                e.preventDefault();
                var $ul = $(this).next('.ins-submenu');
                var $arrow = $(this).find('.ins-arrow');
                var isOpen = $(this).hasClass('open');

                // Close all
                $('.ins-submenu-toggle.open').removeClass('open');
                $('.ins-submenu.open').slideUp(200).removeClass('open');

                if(!isOpen){
                    $(this).addClass('open');
                    $ul.slideDown(220).addClass('open');
                }
            });

            // Auto-open active submenu
            $('.ins-submenu .ins-nav-link.active').closest('.ins-submenu').addClass('open').show()
                .prev('.ins-submenu-toggle').addClass('open');

            // Page wrapper height
            function setPageHeight(){
                var h = $(window).height();
                $('.ins-main').css('min-height', h);
            }
            setPageHeight();
            $(window).on('resize', setPageHeight);
        });
    })(jQuery);
    </script>
