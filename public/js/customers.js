// ฟังก์ชันสำหรับการกรองข้อมูลลูกค้า
function applyFilters() {
    const statusFilter = document.getElementById('statusFilter').value;
    const typeFilter = document.getElementById('customerTypeFilter').value;

    // สร้าง URL parameters
    const params = new URLSearchParams();

    if (statusFilter && statusFilter !== 'all') {
        params.append('status', statusFilter);
    }

    if (typeFilter && typeFilter !== 'all') {
        params.append('type', typeFilter);
    }

    // Redirect ไปยัง URL พร้อม query parameters
    const baseUrl = window.location.pathname;
    const queryString = params.toString();
    window.location.href = queryString ? `${baseUrl}?${queryString}` : baseUrl;
}

// ฟังก์ชันสำหรับการค้นหา (ถ้าต้องการเพิ่มในอนาคต)
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');

    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const tableRows = document.querySelectorAll('#customerTable tbody tr');

            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
