<?php include 'includes/admin_header.php'; ?>
<?php include 'includes/admin_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="admin-content">
        <!-- Topbar -->
        <div class="admin-topbar">
            <div class="topbar-left">
                <h1>⚙️ Store Settings</h1>
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
            <!-- Store Information -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Store Information</h2>
                </div>

                <form class="admin-form" id="storeInfoForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="store-name">Store Name *</label>
                            <input type="text" id="store-name" name="store_name" required 
                                   value="OurStuffies" placeholder="Your store name">
                        </div>

                        <div class="form-group">
                            <label for="store-email">Email Address *</label>
                            <input type="email" id="store-email" name="store_email" required 
                                   value="info@ourstuffies.com" placeholder="store@example.com">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="store-phone">Phone Number *</label>
                            <input type="tel" id="store-phone" name="store_phone" required 
                                   value="+62 859-7490-6945" placeholder="+62 xxx-xxxx-xxxx">
                        </div>

                        <div class="form-group">
                            <label for="store-whatsapp">WhatsApp Number *</label>
                            <input type="tel" id="store-whatsapp" name="store_whatsapp" required 
                                   value="6285974906945" placeholder="628xxxxxxxxxx">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="store-address">Store Address *</label>
                        <textarea id="store-address" name="store_address" required rows="3"
                                  placeholder="Full store address">Blk. A-B No.53b, Gn. Kelua, Kec. Samarinda Ulu, Kota Samarinda, Kalimantan Timur 75243</textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="store-city">City *</label>
                            <input type="text" id="store-city" name="store_city" required 
                                   value="Samarinda" placeholder="City name">
                        </div>

                        <div class="form-group">
                            <label for="store-postal">Postal Code *</label>
                            <input type="text" id="store-postal" name="store_postal" required 
                                   value="75243" placeholder="Postal code">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Store Information</button>
                </form>
            </div>

            <!-- Business Hours -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Business Hours</h2>
                </div>

                <form class="admin-form" id="businessHoursForm">
                    <div class="info-box">
                        <p>⏰ Set your store's operating hours. These will be displayed on your website and used to inform customers.</p>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="opening-time">Opening Time *</label>
                            <input type="time" id="opening-time" name="opening_time" required value="10:00">
                        </div>

                        <div class="form-group">
                            <label for="closing-time">Closing Time *</label>
                            <input type="time" id="closing-time" name="closing_time" required value="17:00">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Operating Days *</label>
                        <div style="display: flex; flex-wrap: wrap; gap: 15px; margin-top: 10px;">
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">
                                <input type="checkbox" name="days[]" value="senin" checked> Senin
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">
                                <input type="checkbox" name="days[]" value="selasa" checked> Selasa
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">
                                <input type="checkbox" name="days[]" value="rabu" checked> Rabu
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">
                                <input type="checkbox" name="days[]" value="kamis" checked> Kamis
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">
                                <input type="checkbox" name="days[]" value="jumat" checked> Jumat
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">
                                <input type="checkbox" name="days[]" value="sabtu" checked> Sabtu
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">
                                <input type="checkbox" name="days[]" value="minggu" checked> Minggu
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="holiday-note">Special Holiday Notes</label>
                        <textarea id="holiday-note" name="holiday_note" rows="2"
                                  placeholder="e.g., Closed on public holidays">⚠️ Orders after closing time will be processed the next day</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Business Hours</button>
                </form>
            </div>

            <!-- Google Maps -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Location & Map</h2>
                </div>

                <form class="admin-form" id="locationForm">
                    <div class="form-group">
                        <label for="maps-embed">Google Maps Embed Code *</label>
                        <textarea id="maps-embed" name="maps_embed" rows="4" required
                                  placeholder='<iframe src="https://www.google.com/maps/embed?..." ...></iframe>'>https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.687140235874!2d117.14484747589199!3d-0.46461823528196516!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df67900560a5033%3A0xd14b9dfd79c14c60!2sOurStuffies!5e0!3m2!1sid!2sid!4v1762506331525!5m2!1sid!2sid</textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="maps-latitude">Latitude</label>
                            <input type="text" id="maps-latitude" name="latitude" 
                                   value="-0.464618" placeholder="e.g., -0.464618">
                        </div>

                        <div class="form-group">
                            <label for="maps-longitude">Longitude</label>
                            <input type="text" id="maps-longitude" name="longitude" 
                                   value="117.147623" placeholder="e.g., 117.147623">
                        </div>
                    </div>

                    <div class="info-box">
                        <p>📍 <strong>How to get Google Maps embed code:</strong><br>
                        1. Open Google Maps and find your store location<br>
                        2. Click "Share" → "Embed a map"<br>
                        3. Copy the iframe code and paste it above</p>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Location Settings</button>
                </form>
            </div>

            <!-- Social Media -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Social Media Links</h2>
                </div>

                <form class="admin-form" id="socialMediaForm">
                    <div class="form-group">
                        <label for="instagram">Instagram URL</label>
                        <input type="url" id="instagram" name="instagram" 
                               placeholder="https://instagram.com/ourstuffies">
                    </div>

                    <div class="form-group">
                        <label for="facebook">Facebook URL</label>
                        <input type="url" id="facebook" name="facebook" 
                               placeholder="https://facebook.com/ourstuffies">
                    </div>

                    <div class="form-group">
                        <label for="twitter">Twitter / X URL</label>
                        <input type="url" id="twitter" name="twitter" 
                               placeholder="https://twitter.com/ourstuffies">
                    </div>

                    <button type="submit" class="btn btn-primary">Save Social Media Links</button>
                </form>
            </div>

            <!-- Delivery Settings -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Delivery Settings</h2>
                </div>

                <form class="admin-form" id="deliveryForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="delivery-fee">Standard Delivery Fee (Rp)</label>
                            <input type="number" id="delivery-fee" name="delivery_fee" 
                                   value="10000" placeholder="10000" min="0">
                        </div>

                        <div class="form-group">
                            <label for="free-delivery">Free Delivery Above (Rp)</label>
                            <input type="number" id="free-delivery" name="free_delivery" 
                                   value="100000" placeholder="100000" min="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="delivery-note">Delivery Notes</label>
                        <textarea id="delivery-note" name="delivery_note" rows="3"
                                  placeholder="Special delivery instructions...">Delivery available throughout Samarinda. Delivery time: 30-45 minutes.</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Delivery Settings</button>
                </form>
            </div>

            <!-- Danger Zone -->
            <div class="content-section" style="border: 2px solid #e74c3c;">
                <div class="section-header">
                    <h2 style="color: #e74c3c;">⚠️ Danger Zone</h2>
                </div>

                <div class="warning-box">
                    <p><strong>Warning:</strong> These actions are irreversible. Please be absolutely certain before proceeding.</p>
                </div>

                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <button class="btn btn-danger" onclick="clearOrders()">Clear All Orders</button>
                    <button class="btn btn-danger" onclick="resetDatabase()">Reset Database</button>
                    <button class="btn btn-danger" onclick="deleteAccount()">Delete Admin Account</button>
                </div>
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

// Form submissions
document.getElementById('storeInfoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Store information saved successfully! (Frontend demo only)');
});

document.getElementById('businessHoursForm').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Business hours saved successfully! (Frontend demo only)');
});

document.getElementById('locationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Location settings saved successfully! (Frontend demo only)');
});

document.getElementById('socialMediaForm').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Social media links saved successfully! (Frontend demo only)');
});

document.getElementById('deliveryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Delivery settings saved successfully! (Frontend demo only)');
});

// Danger zone functions
function clearOrders() {
    if (confirm('Are you ABSOLUTELY SURE you want to clear all orders?\n\nThis action cannot be undone!')) {
        if (confirm('Last confirmation: Delete ALL orders?')) {
            alert('All orders cleared! (Frontend demo only)');
        }
    }
}

function resetDatabase() {
    if (confirm('Are you ABSOLUTELY SURE you want to reset the entire database?\n\nThis will delete ALL data including menu, orders, and customers!\n\nThis action cannot be undone!')) {
        if (confirm('Last confirmation: Reset entire database?')) {
            alert('Database reset! (Frontend demo only)');
        }
    }
}

function deleteAccount() {
    if (confirm('Are you ABSOLUTELY SURE you want to delete your admin account?\n\nYou will lose access to this panel!\n\nThis action cannot be undone!')) {
        const password = prompt('Enter your password to confirm:');
        if (password) {
            alert('Admin account deleted! (Frontend demo only)');
        }
    }
}
</script>

</body>
</html>