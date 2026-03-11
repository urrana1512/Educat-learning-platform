/**
 * Razorpay High-Fidelity Simulation Library
 * Mimics the look and feel of Razorpay popup for demonstration purposes.
 */

const RazorpaySim = {
    init: function(options) {
        this.options = options;
        this.renderOverlay();
    },

    renderOverlay: function() {
        if (document.getElementById('rzp-sim-overlay')) return;

        const overlay = document.createElement('div');
        overlay.id = 'rzp-sim-overlay';
        overlay.className = 'rzp-sim-overlay';
        overlay.innerHTML = `
            <div class="rzp-sim-modal">
                <div class="rzp-sim-close" onclick="RazorpaySim.close()">&times;</div>
                <div class="rzp-sim-sidebar">
                    <div class="rzp-sim-logo">
                        <img src="${this.options.image || 'assets/img/EduCat (4).png'}" alt="Logo">
                    </div>
                    <div class="rzp-sim-badge">Test Mode</div>
                    <div style="text-align:center;">
                        <div style="font-size: 12px; color: rgba(255,255,255,0.6); margin-bottom: 5px;">PAYING</div>
                        <div style="font-size: 18px; font-weight: 700;">₹${(this.options.amount / 100).toFixed(2)}</div>
                    </div>
                </div>
                <div class="rzp-sim-main">
                    <div id="rzp-sim-step1">
                        <h4 style="margin-top:0; margin-bottom: 20px; font-size: 14px; color: #475569;">SELECT PAYMENT METHOD</h4>
                        
                        <div class="rzp-sim-method" onclick="RazorpaySim.process()">
                            <i class="fas fa-university"></i>
                            <div>
                                <div style="font-weight: 600; font-size: 14px;">UPI</div>
                                <div style="font-size: 12px; color: #64748B;">Pay via any UPI App</div>
                            </div>
                        </div>

                        <div class="rzp-sim-method" onclick="RazorpaySim.process()">
                            <i class="fas fa-credit-card"></i>
                            <div>
                                <div style="font-weight: 600; font-size: 14px;">Card</div>
                                <div style="font-size: 12px; color: #64748B;">Visa, Mastercard, RuPay & More</div>
                            </div>
                        </div>

                        <div class="rzp-sim-method" onclick="RazorpaySim.process()">
                            <i class="fas fa-wallet"></i>
                            <div>
                                <div style="font-weight: 600; font-size: 14px;">Netbanking</div>
                                <div style="font-size: 12px; color: #64748B;">All Major Banks Available</div>
                            </div>
                        </div>
                    </div>

                    <div id="rzp-sim-step2" class="rzp-sim-processing">
                        <div class="rzp-sim-spinner"></div>
                        <h3 style="margin-bottom: 10px;">Processing Payment</h3>
                        <p style="color: #64748B; font-size: 14px;">Please wait while we confirm your transaction with the bank.</p>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);
    },

    open: function() {
        const overlay = document.getElementById('rzp-sim-overlay');
        overlay.style.display = 'flex';
        document.getElementById('rzp-sim-step1').style.display = 'block';
        document.getElementById('rzp-sim-step2').style.display = 'none';
    },

    close: function() {
        if (confirm("Are you sure you want to close the payment?")) {
            document.getElementById('rzp-sim-overlay').style.display = 'none';
            if (this.options.modal && this.options.modal.ondismiss) {
                this.options.modal.ondismiss();
            }
        }
    },

    process: function() {
        document.getElementById('rzp-sim-step1').style.display = 'none';
        document.getElementById('rzp-sim-step2').style.display = 'block';

        // Simulate network delay
        setTimeout(() => {
            document.getElementById('rzp-sim-overlay').style.display = 'none';
            if (this.options.handler) {
                this.options.handler({
                    id: 'pay_sim_' + Math.random().toString(36).substr(2, 9),
                    status: 'success'
                });
            }
        }, 2000);
    }
};
