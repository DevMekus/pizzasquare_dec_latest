export default class Paystack {
  constructor({ publicKey }) {
    if (!publicKey) throw new Error("Paystack public key is required");
    this.publicKey = publicKey;
  }

  pay({
    email,
    amount, // in Naira
    reference = this.generateReference(),
    metadata = {},
    onSuccess = () => {},
    onClose = () => {},
    label = "Pay Now",
    currency = "NGN",
  }) {
    if (!email || !amount) {
      alert("Email and amount are required to process payment.");
      return;
    }

    const handler = PaystackPop.setup({
      key: this.publicKey,
      email,
      amount: amount * 100, // Convert to kobo
      currency,
      ref: reference,
      metadata,
      label,
      callback: function (response) {
        onSuccess(response);
      },
      onClose: function () {
        onClose();
      },
    });

    handler.openIframe();
  }

  generateReference() {
    return "REF_" + Math.floor(Math.random() * 1000000000);
  }
}
