<div id="cartDrawer" class="cart-drawer">
    <div class="cart-header">
        <div class="cart-header-title">
            <h3>Your Cart</h3>
            <span class="cart-header-count" id="cartHeaderCount">0 items</span>
        </div>
        <button class="drawer-close" onclick="closeCart()" aria-label="Close cart">&times;</button>
    </div>
    <div class="cart-body" id="cartBody">
        <div class="cart-empty">
            <div class="cart-empty-icon"><i class="fas fa-shopping-bag"></i></div>
            <h4>Your cart is empty</h4>
            <p>Discover luxury frames crafted for you.</p>
            <button class="cart-continue-btn" onclick="closeCart()">Continue Shopping</button>
        </div>
    </div>
    <div class="cart-footer" id="cartFooter" style="display:none">
        <div class="cart-summary">
            <div class="cart-summary-row">
                <span>Items</span>
                <span id="cartItemCount">0</span>
            </div>
            <div class="cart-summary-row cart-summary-total">
                <span class="label">Subtotal</span>
                <strong id="cartSubtotal">₹0</strong>
            </div>
        </div>
        <button class="cart-reset-btn" id="cartResetBtn" onclick="resetCart()"><i class="fas fa-rotate-left"></i> Reset Cart</button>
        <button class="cart-continue-link" onclick="closeCart()">Continue Shopping</button>
    </div>
</div>