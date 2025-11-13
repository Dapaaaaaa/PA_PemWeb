<?php include 'includes/admin_header.php'; ?>
<?php include 'includes/admin_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="admin-content">
        <!-- Topbar -->
        <div class="admin-topbar">
            <div class="topbar-left">
                <h1>📦 Manajemen Pesanan</h1>
            </div>
            <div class="topbar-right">
                <div class="admin-user">
                    <div class="admin-user-avatar">A</div>
                    <div class="admin-user-info">
                        <h4>Admin User</h4>
                        <p>Administrator</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Area -->
        <div class="admin-main">
            <!-- Order Stats -->
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon orange">
                        ⏳
                    </div>
                    <div class="stat-info">
                        <h3>Pending</h3>
                        <div class="stat-number">8</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon blue">
                        🔄
                    </div>
                    <div class="stat-info">
                        <h3>Proses</h3>
                        <div class="stat-number">12</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        ✅
                    </div>
                    <div class="stat-info">
                        <h3>Beres</h3>
                        <div class="stat-number">122</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon red">
                        ❌
                    </div>
                    <div class="stat-info">
                        <h3>Batal</h3>
                        <div class="stat-number">5</div>
                    </div>
                </div>
            </div>

            <!-- Orders List -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Semua Pesanan</h2>
                    <div>
                        <input type="text" placeholder="Search by order ID or customer..." 
                               style="padding: 8px 16px; border: 2px solid #ddd; border-radius: 8px; margin-right: 10px; width: 250px;">
                        <select style="padding: 8px 16px; border: 2px solid #ddd; border-radius: 8px;">
                            <option value="">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Proses</option>
                            <option value="completed">Beres</option>
                            <option value="cancelled">Batal</option>
                        </select>
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Pelanggan</th>
                            <th>No Kontak</th>
                            <th>Item</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>#ORD20250001</strong></td>
                            <td>John Doe</td>
                            <td>+62 812-3456-7890</td>
                            <td>
                                <small>2x Burger Beef<br>1x French Fries</small>
                            </td>
                            <td><strong>Rp 62,000</strong></td>
                            <td><span class="badge badge-success">Beres</span></td>
                            <td>2025-01-15 14:30</td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-success" onclick="viewOrder(1)">Lihat</button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>#ORD20250002</strong></td>
                            <td>Jane Smith</td>
                            <td>+62 813-9876-5432</td>
                            <td>
                                <small>1x Kebab Sapi<br>1x Jus Jeruk</small>
                            </td>
                            <td><strong>Rp 30,000</strong></td>
                            <td><span class="badge badge-warning">Proses</span></td>
                            <td>2025-01-15 13:15</td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-success" onclick="viewOrder(2)">Lihat</button>
                                <button class="btn btn-sm btn-primary" onclick="updateStatus(2)">Update</button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>#ORD20250003</strong></td>
                            <td>Ahmad Rahman</td>
                            <td>+62 815-1234-5678</td>
                            <td>
                                <small>3x Burger Chicken</small>
                            </td>
                            <td><strong>Rp 60,000</strong></td>
                            <td><span class="badge badge-info">Pending</span></td>
                            <td>2025-01-15 12:00</td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-success" onclick="viewOrder(3)">Lihat</button>
                                <button class="btn btn-sm btn-primary" onclick="updateStatus(3)">Update</button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>#ORD20250004</strong></td>
                            <td>Siti Nurhaliza</td>
                            <td>+62 817-9999-8888</td>
                            <td>
                                <small>2x Kebab Ayam<br>2x Es Teh Manis</small>
                            </td>
                            <td><strong>Rp 40,000</strong></td>
                            <td><span class="badge badge-success">Beres</span></td>
                            <td>2025-01-14 16:45</td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-success" onclick="viewOrder(4)">Lihat</button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>#ORD20250005</strong></td>
                            <td>Budi Santoso</td>
                            <td>+62 819-5555-6666</td>
                            <td>
                                <small>1x Burger Beef<br>1x French Fries<br>1x Jus Jeruk</small>
                            </td>
                            <td><strong>Rp 47,000</strong></td>
                            <td><span class="badge badge-warning">Proses</span></td>
                            <td>2025-01-14 15:20</td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-success" onclick="viewOrder(5)">Lihat</button>
                                <button class="btn btn-sm btn-primary" onclick="updateStatus(5)">Update</button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>#ORD20250006</strong></td>
                            <td>Rina Wijaya</td>
                            <td>+62 821-7777-8888</td>
                            <td>
                                <small>1x Kebab Sapi</small>
                            </td>
                            <td><strong>Rp 20,000</strong></td>
                            <td><span class="badge badge-danger">Batal</span></td>
                            <td>2025-01-14 14:00</td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-success" onclick="viewOrder(6)">Lihat</button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>#ORD20250007</strong></td>
                            <td>Dedi Kurniawan</td>
                            <td>+62 822-3333-4444</td>
                            <td>
                                <small>2x Burger Chicken<br>2x Es Teh Manis</small>
                            </td>
                            <td><strong>Rp 50,000</strong></td>
                            <td><span class="badge badge-success">Beres</span></td>
                            <td>2025-01-14 13:30</td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-success" onclick="viewOrder(7)">Lihat</button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>#ORD20250008</strong></td>
                            <td>Lisa Andriani</td>
                            <td>+62 823-1111-2222</td>
                            <td>
                                <small>1x Burger Beef<br>2x French Fries</small>
                            </td>
                            <td><strong>Rp 49,000</strong></td>
                            <td><span class="badge badge-info">Pending</span></td>
                            <td>2025-01-14 11:15</td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-success" onclick="viewOrder(8)">Lihat</button>
                                <button class="btn btn-sm btn-primary" onclick="updateStatus(8)">Update</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Order Detail Modal Placeholder -->
            <div class="info-box">
                <p>💡 <strong>Tip:</strong> Klik "Lihat" untuk melihat detail pesanan. Gunakan "Perbarui" untuk mengubah status pesanan.</p>
            </div>
        </div>
    </div>

</div>

<script>
// Set active menu
document.querySelectorAll('.menu-item').forEach(item => {
    if (item.href === window.location.href) {
        item.classList.add('active');
    }
});

// View order function
function viewOrder(id) {
    alert('Viewing order details #' + id + '\n\nOrder Information:\nCustomer: [Customer Name]\nItems: [Item List]\nTotal: Rp XX,XXX\nAddress: [Delivery Address]\n\n(Frontend demo only)');
}

// Update status function
function updateStatus(id) {
    const newStatus = prompt('Update order status:\n\n1. Pending\n2. Processing\n3. Completed\n4. Cancelled\n\nEnter number (1-4):');
    
    if (newStatus >= 1 && newStatus <= 4) {
        const statuses = ['Pending', 'Processing', 'Completed', 'Cancelled'];
        alert('Order #' + id + ' status updated to: ' + statuses[newStatus - 1] + '\n\n(Frontend demo only)');
    }
}
</script>

</body>
</html>