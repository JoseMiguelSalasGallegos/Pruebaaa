function switchPaymentMethod(method) {
    const cardForm = document.getElementById('card-form');
    const paypalButton = document.getElementById('paypal-button');
    const methods = document.querySelectorAll('.payment-method');

    methods.forEach(el => el.classList.remove('active'));
    
    if (method === 'card') {
      cardForm.classList.remove('hidden');
      paypalButton.classList.add('hidden');
      methods[0].classList.add('active');
    } else {
      cardForm.classList.add('hidden');
      paypalButton.classList.remove('hidden');
      methods[1].classList.add('active');
    }
  }

  document.getElementById('card-number').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
    e.target.value = value;
  });

  document.getElementById('expiry').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
      value = value.slice(0,2) + '/' + value.slice(2);
    }
    e.target.value = value;
  });