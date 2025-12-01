// Elements
const searchInput = document.getElementById('search');
const sortSelect = document.getElementById('sort');
const tagsBtns = Array.from(document.querySelectorAll('.filters .tag'));
const postsContainer = document.getElementById('posts');
const resultsCount = document.getElementById('results-count');
const modal = document.getElementById('modal');
const modalContent = document.getElementById('modal-content');
const viewGridBtn = document.getElementById('view-grid');
const viewListBtn = document.getElementById('view-list');
const addPostBtn = document.getElementById('add-post-btn');

let state = { q:'', tag:'all', sort:'popular', view:'grid' };

function normalize(s){ return (s||'').toString().toLowerCase(); }

// Collect initial DOM post nodes into an array of objects {node, meta}
function collectDOMPosts(){
  const nodes = Array.from(postsContainer.querySelectorAll('.post-card'));
  return nodes.map(node=>{
    return {
      node,
      meta: {
        id: Number(node.dataset.id),
        title: node.dataset.title || node.querySelector('h3')?.textContent || '',
        excerpt: node.dataset.excerpt || node.querySelector('.excerpt')?.textContent || '',
        tags: (node.dataset.tags||'').split(',').map(s=>s.trim()).filter(Boolean),
        date: node.dataset.date || node.dataset.date,
        views: Number(node.dataset.views) || 0
      }
    };
  });
}

function renderPosts(){
  const posts = collectDOMPosts();
  let list = posts.slice();

  // filter tag
  if(state.tag && state.tag!=='all'){
    list = list.filter(p=> p.meta.tags.map(t=>t.toLowerCase()).includes(state.tag));
  }
  // search
  if(state.q) {
    const q = normalize(state.q);
    list = list.filter(p=> normalize(p.meta.title).includes(q) || normalize(p.meta.excerpt).includes(q) || p.meta.tags.join(',').includes(q));
  }
  // sort
  if(state.sort==='popular') list.sort((a,b)=> b.meta.views - a.meta.views);
  if(state.sort==='new') list.sort((a,b)=> new Date(b.meta.date) - new Date(a.meta.date));
  if(state.sort==='alpha') list.sort((a,b)=> a.meta.title.localeCompare(b.meta.title));

  // view mode class
  postsContainer.classList.toggle('list', state.view==='list');

  // re-append nodes in the sorted/filtered order
  postsContainer.innerHTML = '';
  if(list.length===0){
    postsContainer.innerHTML = '<p class="muted">Không tìm thấy bài viết.</p>';
  } else {
    list.forEach(p=>{
      // ensure node has correct view class
      p.node.classList.toggle('list-mode', state.view==='list');
      postsContainer.appendChild(p.node);
    });
  }

  // update count
  resultsCount.textContent = `${list.length} bài viết`;
}

function bindUI(){
  // tags
  tagsBtns.forEach(btn=> btn.addEventListener('click', function(){
    tagsBtns.forEach(b=>b.classList.remove('active'));
    this.classList.add('active');
    state.tag = this.dataset.tag || 'all';
    renderPosts();
  }));
  // search
  if(searchInput) searchInput.addEventListener('input', debounce(function(e){ state.q = this.value; renderPosts(); }, 180));
  // sort
  if(sortSelect) sortSelect.addEventListener('change', function(){ state.sort = this.value; renderPosts(); });
  // read buttons (delegate)
  postsContainer.addEventListener('click', function(e){
    const readBtn = e.target.closest('.read-btn');
    if(readBtn) {
      const id = Number(readBtn.dataset.id);
      openPostModal(id);
      return;
    }
    const editBtn = e.target.closest('.edit-btn');
    if(editBtn) {
      const id = Number(editBtn.dataset.id);
      openEditModal(id);
      return;
    }
    const deleteBtn = e.target.closest('.delete-btn');
    if(deleteBtn) {
      const id = Number(deleteBtn.dataset.id);
      deletePost(id);
      return;
    }
  });
  // add post button
  // if(addPostBtn) addPostBtn.addEventListener('click', openAddModal);
  // view toggle
  if(viewGridBtn) viewGridBtn.addEventListener('click', function(){ state.view='grid'; viewGridBtn.classList.add('active'); viewListBtn.classList.remove('active'); postsContainer.classList.remove('list'); renderPosts(); });
  if(viewListBtn) viewListBtn.addEventListener('click', function(){ state.view='list'; viewListBtn.classList.add('active'); viewGridBtn.classList.remove('active'); postsContainer.classList.add('list'); renderPosts(); });
}

function openPostModal(id){
  // Find the existing post node in the DOM and use its data/content
  const node = postsContainer.querySelector(`.post-card[data-id="${id}"]`);
  if(!node) return;
  const title = node.querySelector('h3')?.textContent || node.dataset.title || '';
  const date = node.dataset.date || '';
  const views = node.dataset.views || '';
  // prefer .post-full HTML content inside the post element if present
  const contentNode = node.querySelector('.post-full');
  const contentHtml = contentNode ? contentNode.innerHTML : escapeHtml(node.dataset.content || '');
  modal.setAttribute('aria-hidden','false');
  modalContent.innerHTML = `<h2>${escapeHtml(title)}</h2><p class='muted'>${escapeHtml(date)} · ${views} views</p><div class='posts'><div class='post'><div>${contentHtml}</div></div></div>`;
}

// close modal
if(modal) modal.addEventListener('click', function(e){ if(e.target.dataset.close==='true') { modal.setAttribute('aria-hidden','true'); modalContent.innerHTML=''; } });

// function openAddModal(){
//   modal.setAttribute('aria-hidden','false');
//   let htmlContent = '<h2>Thêm bài viết mới</h2><form id="add-post-form" style="margin-top:12px;display:flex;flex-direction:column;gap:12px;"><div style="display:flex;flex-direction:column;gap:6px;"><label>Tiêu đề</label><input type="text" id="post-title" placeholder="Tiêu đề bài viết" style="padding:8px 12px;border-radius:8px;border:1px solid rgba(15,23,42,0.1);"/></div><div style="display:flex;flex-direction:column;gap:6px;"><label>Tóm tắt</label><textarea id="post-excerpt" placeholder="Tóm tắt nội dung" style="padding:8px 12px;border-radius:8px;border:1px solid rgba(15,23,42,0.1);height:60px;"></textarea></div><div style="display:flex;flex-direction:column;gap:6px;"><label>Tags (cách nhau bằng dấu phẩy)</label><input type="text" id="post-tags" placeholder="tag1, tag2, tag3" style="padding:8px 12px;border-radius:8px;border:1px solid rgba(15,23,42,0.1);"/></div><div style="display:flex;gap:8px;margin-top:8px;"><button type="submit" class="btn-primary" style="flex:1;background:linear-gradient(90deg,var(--accent),var(--accent2));color:#04202a;">Thêm</button><button type="button" class="btn-secondary" data-close="true" style="flex:1;padding:8px 12px;border-radius:10px;border:1px solid rgba(15,23,42,0.1);background:transparent;cursor:pointer;">Hủy</button></div></form>';
//   modalContent.innerHTML = htmlContent;
//   document.getElementById('add-post-form').addEventListener('submit', function(e){
//     e.preventDefault();
//     const title = document.getElementById('post-title').value.trim();
//     const excerpt = document.getElementById('post-excerpt').value.trim();
//     const tagsStr = document.getElementById('post-tags').value.trim();
//     if(!title || !excerpt){
//       alert('Vui lòng điền đầy đủ thông tin.');
//       return;
//     }
//     const tags = tagsStr.split(',').map(t=>t.trim()).filter(Boolean).join(',');
//     const newId = Math.max(...collectDOMPosts().map(p=>p.meta.id), 0) + 1;
//     const newPost = document.createElement('article');
//     newPost.className = 'post-card';
//     newPost.dataset.id = newId;
//     newPost.dataset.title = title;
//     newPost.dataset.excerpt = excerpt;
//     newPost.dataset.tags = tags;
//     newPost.dataset.date = new Date().toISOString().split('T')[0];
//     newPost.dataset.views = 0;
//     let tagsHtml = tags.split(',').map(t => '<span>' + escapeHtml(t) + '</span>').join('');
//     newPost.innerHTML = '<div class="content"><h3>' + escapeHtml(title) + '</h3><p class="excerpt">' + escapeHtml(excerpt) + '</p><div class="bottom"><div class="tags">' + tagsHtml + '</div><div class="actions"><button class="read-btn" data-id="' + newId + '">Đọc</button><button class="edit-btn" data-id="' + newId + '" title="Sửa"><i class="bx bx-edit"></i></button><button class="delete-btn" data-id="' + newId + '" title="Xóa"><i class="bx bx-trash"></i></button></div></div></div><div class="post-full" style="display:none">Nội dung chi tiết bài viết...</div>';
//     postsContainer.insertBefore(newPost, postsContainer.firstChild);
//     alert('Bài viết đã được thêm thành công!');
//     modal.setAttribute('aria-hidden','true');
//     modalContent.innerHTML='';
//     renderPosts();
//   });
//   const cancelBtn = modalContent.querySelector('[data-close="true"]');
//   if(cancelBtn) cancelBtn.addEventListener('click', ()=>{ modal.setAttribute('aria-hidden','true'); modalContent.innerHTML=''; });
// }

function openEditModal(id){
  // Redirect to edit-post.html with post ID
  // Later you can pass ID via URL: edit-post.html?id=1
  window.location.href = 'edit-post.html';
}

function deletePost(id){
  if(!confirm('Bạn chắc chắn muốn xóa bài viết này?')) return;
  const node = postsContainer.querySelector('.post-card[data-id="' + id + '"]');
  if(node){
    node.remove();
    alert('Bài viết đã được xóa!');
    renderPosts();
  }
}

function escapeHtml(str){ return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function debounce(fn,delay){ let t; return function(){ clearTimeout(t); t=setTimeout(()=>fn.apply(this,arguments),delay); }; }

// init
bindUI(); renderPosts();
