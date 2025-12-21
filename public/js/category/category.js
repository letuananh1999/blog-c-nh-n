// cate.js - Giữ nguyên giao diện + thêm logic thực tế
(function () {

  /* DOM ELEMENTS */
  const modalRoot = document.getElementById('modal-root');
  const addBtn = document.getElementById('add-cat');
  const cardsRoot = document.querySelector('.cards-grid');
  const tbody = document.querySelector('#cat-table tbody');

  /* ===== HELPER: Hiển thị modal form đẹp ===== */
  function showFormModal(title = 'Form', prefillName = '', prefillDesc = '', id = null) {
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.style.cssText = `
      position: fixed;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      background: linear-gradient(rgba(2, 6, 23, 0.4), rgba(2, 6, 23, 0.5));
      backdrop-filter: blur(2px);
      transition: opacity 0.2s ease;
    `;

    const panel = document.createElement('div');
    panel.style.cssText = `
      background: #ffffff;
      border-radius: 16px;
      padding: 32px;
      min-width: 380px;
      max-width: 500px;
      box-shadow: 0 20px 60px rgba(6, 25, 40, 0.15);
      border: 1px solid rgba(60, 145, 230, 0.1);
      animation: slideUp 0.3s ease;
      transition: all 0.2s ease;
    `;

    panel.innerHTML = `
      <style>
        @keyframes slideUp { from {opacity:0; transform:translateY(20px);} to {opacity:1; transform:translateY(0);} }
        .form-group { margin-bottom: 20px; }
        .form-group label { display:block; font-size:14px; font-weight:600; color:#04202a; margin-bottom:8px; }
        .form-group input, .form-group textarea { width:100%; padding:12px 14px; border:1.5px solid #e5e7eb; border-radius:10px; font-family:inherit; font-size:14px; color:#04202a; transition:all 0.2s ease; box-sizing:border-box; }
        .form-group input:focus, .form-group textarea:focus { outline:none; border-color:#3C91E6; box-shadow:0 0 0 3px rgba(60,145,230,0.1); }
        .form-group textarea { resize:vertical; min-height:100px; }
      </style>

      <div style="display:flex; align-items:center; gap:12px; margin-bottom:24px; padding-bottom:16px; border-bottom:1px solid #f0f0f0;">
        <div style="font-size:24px;">📋</div>
        <div>
          <h2 style="margin:0; font-size:20px; font-weight:700; color:#04202a;">${title}</h2>
          <p style="margin:4px 0 0; font-size:12px; color:#6b7280;">Quản lý danh mục của bạn</p>
        </div>
      </div>

      <form id="f-form" style="display:flex; flex-direction:column; gap:0;">
        <div class="form-group">
          <label for="f-name"><i style="color:#3C91E6;">🏷️</i> Tên danh mục *</label>
          <input id="f-name" type="text" placeholder="Nhập tên danh mục (e.g., Design, Marketing...)" value="${prefillName}" />
        </div>

        <div class="form-group">
          <label for="f-desc"><i style="color:#3C91E6;">📝</i> Mô tả</label>
          <textarea id="f-desc" placeholder="Mô tả chi tiết về danh mục này...">${prefillDesc}</textarea>
        </div>

        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:24px; padding-top:16px; border-top:1px solid #f0f0f0;">
          <button id="f-cancel" type="button" style="padding:10px 20px; background:transparent; border:1.5px solid #e5e7eb; color:#04202a; border-radius:10px; cursor:pointer; font-weight:500; font-size:14px; transition:all 0.2s ease;"
            onmouseover="this.style.background='#f9fafb';this.style.borderColor='#d1d5db';" 
            onmouseout="this.style.background='transparent';this.style.borderColor='#e5e7eb';">✕ Hủy</button>
          <button id="f-save" type="submit" style="padding:10px 24px; background:linear-gradient(90deg,#3C91E6,#6dd5ed); color:#04202a; border:none; border-radius:10px; cursor:pointer; font-weight:600; font-size:14px; transition:all 0.2s ease; box-shadow:0 4px 15px rgba(60,145,230,0.3);"
            onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(60,145,230,0.4)';"
            onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 15px rgba(60,145,230,0.3)';">✓ Lưu</button>
        </div>
      </form>
    `;

    modal.appendChild(panel);
    modalRoot.appendChild(modal);

    const closeModal = () => {
      panel.style.opacity = '0';
      panel.style.transform = 'translateY(20px)';
      modal.style.opacity = '0';
      setTimeout(() => { modal.remove(); document.removeEventListener('keydown', escHandler); }, 200);
    };

    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    const escHandler = ev => { if (ev.key === 'Escape') closeModal(); };
    document.addEventListener('keydown', escHandler);
    panel.querySelector('#f-cancel').onclick = closeModal;

    // Form submit: thêm/sửa thực tế
    panel.querySelector('#f-form').addEventListener('submit', async (e) => {
      e.preventDefault();
      const name = panel.querySelector('#f-name').value.trim();
      const desc = panel.querySelector('#f-desc').value.trim();
      if (!name) { alert('⚠️ Vui lòng nhập tên danh mục'); return; }

      try {
        const url = id ? `/admin/categories/${id}` : '/admin/categories';
        const method = id ? 'PUT' : 'POST';

        const res = await fetch(url, {
          method,
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({ name, description: desc, _method: method })
        });
        const data = await res.json();

        if (data.status) {
          alert('🎉 Lưu thành công!');
          closeModal();
          location.reload();
        } else {
          alert('❌ Lỗi: ' + (data.message || 'Không xác định'));
        }
      } catch (err) {
        console.error('Fetch error:', err);
        alert('❌ Có lỗi xảy ra: ' + err.message);
      }
    });
  }

  /* ===== EVENT: Click nút "Thêm danh mục" ===== */
  if (addBtn) {
    addBtn.addEventListener('click', () => showFormModal('➕ Thêm danh mục'));
  }

  /* ===== EVENT: Click card danh mục (sửa) ===== */
  if (cardsRoot) {
    cardsRoot.addEventListener('click', e => {
      const card = e.target.closest('.cat-card');
      if (card) {
        const id = card.dataset.id; // Thêm data-id trong Blade
        const name = card.querySelector('.card-title')?.textContent || '';
        const desc = card.querySelector('.muted')?.textContent || '';
        showFormModal(`✏️ Sửa danh mục: ${name}`, name, desc, id);
      }
    });
  }

  /* ===== EVENT: Click nút "Sửa" trong bảng ===== */
  if (tbody) {
    tbody.addEventListener('click', e => {
      const editBtn = e.target.closest('button');
      if (editBtn && editBtn.textContent.includes('Sửa')) {
        const row = editBtn.closest('tr');
        const id = row.dataset.id; // cần <tr data-id="{{ $cat->id }}">
        const name = row?.children[1]?.textContent || '';
        const desc = row?.children[2]?.textContent || '';
        showFormModal(`✏️ Sửa danh mục: ${name}`, name, desc, id);
      }
    });
  }

  /* ===== EVENT: Click nút "Xóa" trong bảng ===== */
  if (tbody) {
    tbody.addEventListener('click', e => {
      const delBtn = e.target.closest('button');
      if (delBtn && delBtn.textContent.includes('Xóa')) {
        const row = delBtn.closest('tr');
        const id = row.dataset.id;
        const name = row?.children[1]?.textContent || 'danh mục này';
        if (confirm(`🗑️ Bạn chắc chắn muốn xóa "${name}"?`)) {
          fetch(`/admin/categories/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
          }).then(res => res.json()).then(data => {
            if (data.status) { alert('🗑️ Xóa thành công!'); location.reload(); }
            else alert('❌ Lỗi: ' + (data.message || 'Không xác định'));
          }).catch(err => { console.error('Delete error:', err); alert('❌ Có lỗi xảy ra: ' + err.message); });
        }
      }
    });
  }

})();

// Table responsive data-label
document.addEventListener('DOMContentLoaded', () => {
  const table = document.getElementById('cat-table');
  if (!table) return;
  const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
  const rows = table.querySelectorAll('tbody tr');
  rows.forEach(tr => {
    Array.from(tr.children).forEach((td, i) => {
      if (!td.hasAttribute('data-label')) td.setAttribute('data-label', headers[i] || '');
    });
  });

  // ===== SEARCH: Tìm kiếm danh mục =====
  // const searchInput = document.getElementById('cate-search');
  // const searchBtn = document.getElementById('cate-search-btn');
  // const tbody = document.querySelector('#cat-table tbody');

  // if (searchInput && searchBtn && tbody) {
  //   const performSearch = async () => {
  //     const query = searchInput.value.trim();

  //     if (query.length === 0) {
  //       location.reload(); // Reload để hiển thị tất cả
  //       return;
  //     }

  //     try {
  //       const response = await fetch(`/admin/categories/search?q=${encodeURIComponent(query)}`);
  //       const data = await response.json();

  //       if (!data.status) {
  //         alert('⚠️ ' + data.message);
  //         return;
  //       }

  //       // Clear bảng hiện tại
  //       tbody.innerHTML = '';

  //       // Render kết quả search
  //       if (data.data && data.data.length > 0) {
  //         data.data.forEach(category => {
  //           const row = document.createElement('tr');
  //           row.dataset.id = category.id;
  //           row.innerHTML = `
  //             <td data-label="ID">${category.id}</td>
  //             <td data-label="Tên danh mục">${category.name}</td>
  //             <td data-label="Mô tả">${category.description || ''}</td>
  //             <td data-label="Số bài">${category.posts_count}</td>
  //             <td data-label="Ngày tạo">${new Date(category.created_at).toLocaleDateString('vi-VN')}</td>
  //             <td data-label="Hành động">
  //               <button class="btn small">Sửa</button>
  //               <button class="btn small danger">Xóa</button>
  //             </td>
  //           `;
  //           tbody.appendChild(row);
  //         });
  //       } else {
  //         const emptyRow = document.createElement('tr');
  //         emptyRow.innerHTML = '<td colspan="6" style="text-align:center; padding:20px; color:#999;">Không tìm thấy danh mục nào 😢</td>';
  //         tbody.appendChild(emptyRow);
  //       }
  //     } catch (error) {
  //       console.error('Search error:', error);
  //       alert('❌ Lỗi tìm kiếm: ' + error.message);
  //     }
  //   };

  //   // Sự kiện: Click nút tìm kiếm
  //   searchBtn.addEventListener('click', performSearch);

  //   // Sự kiện: Nhấn Enter trong input
  //   searchInput.addEventListener('keypress', (e) => {
  //     if (e.key === 'Enter') {
  //       performSearch();
  //     }
  //   });
  // }
});
