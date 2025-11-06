// dangnhap.js - Xử lý form đăng nhập (Optimized)

(function() {
  'use strict';

  const form = document.getElementById('login-form');
  const inputs = {
    email: document.getElementById('email'),
    password: document.getElementById('password'),
    remember: document.getElementById('remember')
  };

  const rules = {
    email: [
      { validate: v => v.trim().length > 0, msg: 'Email không được để trống' },
      { validate: v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v), msg: 'Email không hợp lệ' }
    ],
    password: [
      { validate: v => v.length > 0, msg: 'Mật khẩu không được để trống' },
      { validate: v => v.length >= 8, msg: 'Mật khẩu phải có ít nhất 8 ký tự' }
    ]
  };

  const setError = (fieldName, message) => {
    const group = inputs[fieldName].closest('.form-group');
    group.classList.add('error');
    group.querySelector('.form-error').textContent = message;
  };

  const clearError = (fieldName) => {
    const group = inputs[fieldName].closest('.form-group');
    group.classList.remove('error');
    group.querySelector('.form-error').textContent = '';
  };

  const validateField = (fieldName) => {
    if (!inputs[fieldName] || !rules[fieldName]) return true;
    const value = inputs[fieldName].value;
    for (const rule of rules[fieldName]) {
      if (!rule.validate(value)) {
        setError(fieldName, rule.msg);
        return false;
      }
    }
    clearError(fieldName);
    return true;
  };

  // Toggle password visibility
  document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      const input = document.getElementById(btn.dataset.target);
      const isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      btn.querySelector('i').className = isHidden ? 'bx bx-show' : 'bx bx-hide';
    });
  });

  // Real-time validation
  Object.keys(inputs).forEach(key => {
    if (inputs[key] && key !== 'remember') {
      inputs[key].addEventListener('blur', () => validateField(key));
      inputs[key].addEventListener('input', function() {
        if (this.closest('.form-group').classList.contains('error')) validateField(key);
      });
    }
  });

  // Form submission
  form.addEventListener('submit', e => {
    e.preventDefault();
    if (!['email', 'password'].every(validateField)) return;

    const formData = {
      email: inputs.email.value.trim(),
      password: inputs.password.value,
      remember: inputs.remember.checked
    };

    console.log('Form Data:', formData);
    // TODO: Connect backend API
    alert('Đăng nhập thành công! (Demo mode)');
  });

  // Remember email functionality
  window.addEventListener('load', () => {
    const saved = localStorage.getItem('remembered-email');
    if (saved) {
      inputs.email.value = saved;
      inputs.remember.checked = true;
    }
  });

  form.addEventListener('change', e => {
    if (e.target === inputs.remember) {
      inputs.remember.checked && inputs.email.value
        ? localStorage.setItem('remembered-email', inputs.email.value)
        : localStorage.removeItem('remembered-email');
    }
  });

})();
