// File: public/js/midtrans-handler.js

/**
 * Midtrans Payment Handler
 * Handle Midtrans Snap payment flow with proper callbacks
 */

class MidtransHandler {
  constructor(snapToken, orderId) {
    this.snapToken = snapToken;
    this.orderId = orderId;
  }

  /**
   * Open Midtrans Snap payment modal
   */
  pay() {
    window.snap.pay(this.snapToken, {
      onSuccess: (result) => {
        console.log('Payment Success:', result);
        this.handleSuccess(result);
      },
      onPending: (result) => {
        console.log('Payment Pending:', result);
        this.handlePending(result);
      },
      onError: (result) => {
        console.error('Payment Error:', result);
        this.handleError(result);
      },
      onClose: () => {
        console.log('Payment modal closed');
        this.handleClose();
      }
    });
  }

  /**
   * Handle successful payment
   */
  handleSuccess(result) {
    // Update payment info to database before redirect
    this.updatePaymentInfo(result).then(() => {
      window.location.href = `/payment/finish/${this.orderId}`;
    });
  }

  /**
   * Handle pending payment
   */
  handlePending(result) {
    // Update payment info to database before redirect
    this.updatePaymentInfo(result).then(() => {
      window.location.href = `/payment/finish/${this.orderId}`;
    });
  }

  /**
   * Handle payment error
   */
  handleError(result) {
    alert('Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
    window.location.href = `/payment/finish/${this.orderId}`;
  }

  /**
   * Handle modal close (user cancelled)
   */
  handleClose() {
    // Still redirect to finish page to show payment info
    window.location.href = `/payment/finish/${this.orderId}`;
  }

  /**
   * Update payment info to database via AJAX
   * This ensures payment code and other info are saved before redirect
   */
  async updatePaymentInfo(result) {
    try {
      const response = await fetch(`/payment/update-info/${this.orderId}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
          midtrans_response: result
        })
      });

      if (!response.ok) {
        console.error('Failed to update payment info');
      }
    } catch (error) {
      console.error('Error updating payment info:', error);
    }
  }
}

/**
 * Initialize payment when button is clicked
 */
function initializePayment(paymentType, bank = null) {
  const tagihanId = document.getElementById('tagihan-id').value;
  
  // Show loading
  const submitBtn = event.target;
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

  // Create transaction
  fetch('/payment/create-transaction', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
      tagihan_id: tagihanId,
      payment_type: paymentType,
      bank: bank
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Initialize Midtrans handler
      const handler = new MidtransHandler(data.snap_token, data.order_id);
      handler.pay();
    } else {
      alert('Gagal membuat transaksi: ' + data.message);
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<i class="bi bi-credit-card me-2"></i>Bayar Sekarang';
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Terjadi kesalahan. Silakan coba lagi.');
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="bi bi-credit-card me-2"></i>Bayar Sekarang';
  });
}