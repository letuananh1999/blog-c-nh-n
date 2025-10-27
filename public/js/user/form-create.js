	// Enhance: password toggle, avatar preview & removal, and demo submit
		document.addEventListener('DOMContentLoaded', ()=>{
			// password toggle
			const pw = document.getElementById('u-password');
			const toggle = document.getElementById('pw-toggle');
			if(toggle && pw){
				toggle.addEventListener('click', ()=>{
					pw.type = pw.type === 'password' ? 'text' : 'password';
					const icon = toggle.querySelector('i');
					icon.classList.toggle('bx-low-vision');
					icon.classList.toggle('bx-show');
				});
			}

			// avatar preview
			const avatarInput = document.getElementById('u-avatar');
			const previewWrap = document.getElementById('avatarPreview');
			const removeBtn = document.getElementById('remove-avatar');

			function showPreview(file){
				if(!file) return;
				const img = document.createElement('img');
				img.alt = 'avatar';
				img.src = URL.createObjectURL(file);
				// clear
				previewWrap.innerHTML = '';
				previewWrap.appendChild(img);
			}

			avatarInput.addEventListener('change', (e)=>{
				const f = e.target.files && e.target.files[0];
				if(!f) return;
				if(!f.type.startsWith('image/')){
					alert('Vui lòng chọn tệp hình ảnh.');
					return;
				}
				showPreview(f);
			});

			removeBtn.addEventListener('click', ()=>{
				avatarInput.value = '';
				previewWrap.innerHTML = '<div class="avatar-placeholder">Ảnh đại diện<br><small>(png, jpg)</small></div>';
			});

			// submit (demo)
			document.getElementById('add-user-form').addEventListener('submit', function(e){
				e.preventDefault();
				const form = new FormData(this);
				// build a demo summary
				const summary = {
					name: form.get('name'),
					email: form.get('email'),
					role: form.get('role'),
					avatar: avatarInput.files && avatarInput.files[0] ? avatarInput.files[0].name : null
				};
				alert('Demo: user đã được thêm (không lưu thực tế)\n' + JSON.stringify(summary, null, 2));
				this.reset();
				removeBtn.click();
			});

			// cancel
			document.getElementById('cancel').addEventListener('click', ()=>{ document.getElementById('add-user-form').reset(); removeBtn.click(); });
		});