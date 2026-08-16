<div id="filterDrawer" class="filter-drawer">
    <div class="filter-header">
        <h3>Filters</h3>
        <button class="drawer-close" onclick="closeDrawer()">&times;</button>
    </div>
    <form id="filterForm" onsubmit="event.preventDefault(); filterProducts();">
        <div class="filter-section" id="categoryFilterSection">
            <label>Category</label>
            <select name="category" id="category">
                <option value="">All Categories</option>
            </select>
        </div>
        <div class="filter-section">
            <label>Gender</label>
            <select name="gender" id="gender">
                <option value="">All</option>
            </select>
        </div>
        <div class="filter-section">
            <label>Brand</label>
            <select name="brand_id" id="brand">
                <option value="">All Brands</option>
            </select>
        </div>
        <div class="filter-section">
            <label>Shape</label>
            <select name="frame_shape" id="shape">
                <option value="">All</option>
            </select>
        </div>
        <div class="filter-section">
            <label>Material</label>
            <select name="frame_material" id="material">
                <option value="">All</option>
            </select>
        </div>
        <div class="filter-section">
            <label>Color</label>
            <select name="frame_color" id="color">
                <option value="">All</option>
            </select>
        </div>
        <div class="filter-section">
            <label>Size</label>
            <select name="frame_size" id="size">
                <option value="">All</option>
            </select>
        </div>
        <div class="filter-section">
            <label>Price Range</label>
            <div class="price-range">
                <input type="number" name="min_price" id="min_price" placeholder="Min" min="0">
                <span>—</span>
                <input type="number" name="max_price" id="max_price" placeholder="Max" min="0">
            </div>
        </div>
        <div class="filter-section">
            <label>Search</label>
            <input type="text" name="search" id="searchInput" placeholder="Model No or BQ Number...">
        </div>
        <div class="filter-section filter-checkboxes">
            <label class="filter-check">
                <input type="checkbox" name="is_new_arrival" value="1">
                <span>New Arrivals</span>
            </label>
            <label class="filter-check">
                <input type="checkbox" name="is_on_sale" value="1">
                <span>On Sale</span>
            </label>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn-apply">Apply Filters</button>
            <button type="reset" class="btn-reset" onclick="document.getElementById('filterForm').reset(); filterProducts();">Reset</button>
        </div>
    </form>
</div>
