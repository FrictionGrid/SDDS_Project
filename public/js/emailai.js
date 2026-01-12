// public/js/emailai.js

(() => {
  let currentDraftId = null;

  const $ = (id) => document.getElementById(id);

  function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : null;
  }

  async function fetchJson(url, options = {}) {
    const res = await fetch(url, options);

    // กันเคส route ผิด / ได้ HTML กลับมา
    const contentType = res.headers.get('content-type') || '';
    if (!contentType.includes('application/json')) {
      const text = await res.text();
      throw new Error(
        `Expected JSON but got "${contentType}". Status=${res.status}. Body preview:\n` +
          text.slice(0, 300)
      );
    }

    const data = await res.json();

    if (!res.ok) {
      // กรณี API ส่ง json error
      const msg = data?.message || `Request failed with status ${res.status}`;
      throw new Error(msg);
    }

    return data;
  }

  function setEditorLoading(isLoading) {
    const btnUpdate = $('btnUpdate');
    const btnSend = $('btnSend');

    if (btnUpdate) btnUpdate.disabled = isLoading;
    if (btnSend) btnSend.disabled = isLoading;

    // optional: ใส่ class loading ได้ถ้าคุณมี CSS
    // document.body.classList.toggle('is-loading', isLoading);
  }

  function setEditorFields({ to_email = '', subject = '', body = '' }) {
    const to = $('emailTo');
    const subjectEl = $('emailSubject');
    const bodyEl = $('emailContent');

    if (to) to.value = to_email || '';
    if (subjectEl) subjectEl.value = subject || '';
    if (bodyEl) bodyEl.value = body || '';
  }

  async function loadDraft(id) {
    if (!id) return;

    currentDraftId = id;
    setEditorLoading(true);

    try {
      const data = await fetchJson(`/email_ai/${id}`);
      setEditorFields(data);
    } catch (err) {
      console.error('Load draft failed:', err);
      alert('โหลด Draft ไม่สำเร็จ (ดู Console เพิ่มเติม)');
    } finally {
      setEditorLoading(false);
    }
  }

  function setActiveCardById(id) {
    document.querySelectorAll('.draft-card').forEach((c) => {
      c.classList.toggle('active', String(c.dataset.draftId) === String(id));
    });
  }

  async function updateCurrentDraft() {
    if (!currentDraftId) {
      alert('ยังไม่ได้เลือก Draft');
      return;
    }

    const csrf = getCsrfToken();
    if (!csrf) {
      alert('ไม่พบ CSRF token (ตรวจสอบว่า layout มี meta csrf-token)');
      return;
    }

    const payload = {
      subject: $('emailSubject') ? $('emailSubject').value : '',
      body: $('emailContent') ? $('emailContent').value : '',
    };

    setEditorLoading(true);

    try {
      const data = await fetchJson(`/email_ai/${currentDraftId}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json',
        },
        body: JSON.stringify(payload),
      });

      // data = { success: true, message: 'Draft updated' } ตามที่ controller ส่งกลับ
      alert(data?.message || 'อัปเดต Draft สำเร็จ');

      // OPTIONAL: อัปเดต subject ในฝั่งซ้ายให้ตรง (ถ้าอยาก)
      const activeCard = document.querySelector(`.draft-card.active[data-draft-id="${currentDraftId}"]`);
      if (activeCard) {
        const subjectEl = activeCard.querySelector('.draft-card__subject');
        if (subjectEl) subjectEl.textContent = payload.subject || '(no subject)';
      }
    } catch (err) {
      console.error('Update draft failed:', err);
      alert('อัปเดต Draft ไม่สำเร็จ (ดู Console เพิ่มเติม)');
    } finally {
      setEditorLoading(false);
    }
  }

  // OPTIONAL: ถ้าคุณมีปุ่มลบใน editor และอยากใช้
  async function deleteCurrentDraft() {
    if (!currentDraftId) return;

    const csrf = getCsrfToken();
    if (!csrf) {
      alert('ไม่พบ CSRF token');
      return;
    }

    if (!confirm('ต้องการลบ Draft นี้หรือไม่?')) return;

    setEditorLoading(true);

    try {
      const data = await fetchJson(`/email_ai/${currentDraftId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json',
        },
      });

      alert(data?.message || 'ลบ Draft สำเร็จ');

      // ลบการ์ดออกจาก list
      const card = document.querySelector(`.draft-card[data-draft-id="${currentDraftId}"]`);
      if (card) card.remove();

      // รีเซ็ต editor
      currentDraftId = null;
      setEditorFields({ to_email: '', subject: '', body: '' });

      // โหลด draft ตัวแรกถ้ายังมี
      const first = document.querySelector('.draft-card');
      if (first) {
        first.classList.add('active');
        await loadDraft(first.dataset.draftId);
      }
    } catch (err) {
      console.error('Delete draft failed:', err);
      alert('ลบ Draft ไม่สำเร็จ (ดู Console เพิ่มเติม)');
    } finally {
      setEditorLoading(false);
    }
  }

  function bindUI() {
    const draftCards = document.querySelectorAll('.draft-card');
    if (draftCards.length === 0) return;

    // โหลด draft แรกอัตโนมัติ
    const firstId = draftCards[0].dataset.draftId;
    setActiveCardById(firstId);
    loadDraft(firstId);

    // คลิกแล้วโหลด
    draftCards.forEach((card) => {
      card.addEventListener('click', () => {
        const id = card.dataset.draftId;
        setActiveCardById(id);
        loadDraft(id);
      });
    });

    // ปุ่ม Update
    const btnUpdate = $('btnUpdate');
    if (btnUpdate) {
      btnUpdate.addEventListener('click', (e) => {
        e.preventDefault();
        updateCurrentDraft();
      });
    }

    // ปุ่ม Send (ยังไม่ผูก เพราะคุณยังไม่ได้ทำ API send)
    const btnSend = $('btnSend');
    if (btnSend) {
      btnSend.addEventListener('click', (e) => {
        e.preventDefault();
        alert('ปุ่ม Send ยังไม่ได้เชื่อม API (ทำขั้นถัดไปได้)');
      });
    }

    // ถ้าคุณเพิ่มปุ่มลบในอนาคต (เช่น id="btnDelete") ก็เปิดใช้ได้
    // const btnDelete = $('btnDelete');
    // if (btnDelete) btnDelete.addEventListener('click', (e) => { e.preventDefault(); deleteCurrentDraft(); });
  }

  document.addEventListener('DOMContentLoaded', bindUI);
})();
