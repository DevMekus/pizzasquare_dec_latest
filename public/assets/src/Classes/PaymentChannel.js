import { CONFIG } from "../Utils/config.js";
import { fetchEncryptionKey } from "./Session.js";
import Paystack from "./Paystack.js";
import Utility from "./Utility.js";
import { HttpRequest } from "../Utils/httpRequest.js";
/**
 *
 *
 * Handles payments through Paystack.
 * Integrates with Utility, Session, and API helpers for
 * a complete verification payment flow.
 *
 * Dependencies:
 *  - CONFIG: Global configuration values.
 *  - fetchEncryptionKey: Retrieves encryption keys for secure transactions.
 *  - Paystack: Wrapper for Paystack SDK integration.
 *  - Utility: Provides UI helpers (confirmation, toasts, etc.).
 *  - HttpRequest: Handles API communication.
 */

export default class PaymentChannel {
  /**
   * Initiates Paystack payment flow for verification services.
   *
   * @param {Object} paymentData - Details required for the payment.
   * @param {string} paymentData.email_address - Payer email.
   * @param {string} paymentData.name - Payer full name.
   * @param {number} paymentData.amount - Payment amount (in Naira).
   *
   * @returns {Promise<void>}
   */
  static async payWithPaystack(paymentData) {
    return new Promise(async (resolve, reject) => {
      try {
        if (
          !paymentData?.name ||
          !paymentData?.email_address ||
          !paymentData?.amount
        ) {
          Utility.toast("Invalid payment data. Please try again.", "error");
          return resolve(false);
        }

        const result = await Utility.confirm(
          "Make payment",
          `You are about to pay ${Utility.fmtNGN(paymentData.amount)}`
        );

        if (!result?.isConfirmed) {
          return resolve(false);
        }

        Swal.close();

        const getKey = await fetchEncryptionKey();
        if (!getKey?.success || !getKey.PAYSTACK_PK) {
          Utility.toast(
            "Unable to fetch payment key. Try again later.",
            "error"
          );
          return resolve(false);
        }

        Utility.toast("Please wait...");

        const paystack = new Paystack({
          publicKey: getKey.PAYSTACK_PK,
        });

        paystack.pay({
          email: paymentData.email_address,
          amount: Number(paymentData.amount),
          metadata: {
            custom_fields: [{ display_name: "Name", value: paymentData.name }],
          },

          onSuccess: async function (response) {
            try {
              /**
               * verify the payment
               */

              Utility.alertLoader()

              const httpRes = await HttpRequest(
                `${CONFIG.API}/payment/paystack`,
                {
                  reference: response.reference,
                  order_id: paymentData.order_id,
                },
                "POST"
              );


              Utility.clearAlertLoader()

              Utility.SweetAlertResponse(response);

              resolve(httpRes);
            } catch (err) {
            
              Swal.fire({
                title: "Verification Failed",
                text: "Payment verification failed.",
                icon: "error",
                confirmButtonColor: "#d51d28",
                n,
              });
              reject(err);
            }
          },

          onClose: function () {
            Swal.fire({
              title: "Payment Cancelled",
              text: "Payment window closed.",
              icon: "error",
              confirmButtonColor: "#d51d28",
              n,
            });
            resolve(false);
          },
        });
      } catch (error) {
        Utility.toast(
          "An unexpected error occurred. Try again later.",
          "error"
        );
        reject(error);
      }
    });
  }
}
