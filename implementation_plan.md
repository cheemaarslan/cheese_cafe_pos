# Convert Cart System from LocalStorage to Database

This plan outlines the steps required to migrate the cart system (currently relying on Vuex Persistedstate / `localStorage`) to a robust database-backed system. This enables cross-device cart persistence and better cart analytics, while keeping guest support intact.

## User Review Required

> [!WARNING]
> **POS Cart Migration**
> There are two cart systems in the Vuex store: `frontendCart` (for end users) and `posCart` (for admin/Point of Sale). Moving the POS cart to the database could slow down fast-paced administrative workflows due to network latency on every add/remove action.
>
> _I recommend migrating ONLY `frontendCart` first._ Please confirm if you want `posCart` converted as well.

> [!CAUTION]
> **Existing Guest Carts**
> If a user currently has a cart in their browser's `localStorage`, these items will be lost upon deployment of this change unless we write a specific frontend script to sync existing `localStorage` items to the DB on their next visit. Is a seamless migration for existing guest carts required?

## Open Questions

> [!IMPORTANT]
>
> - Do you want a scheduled job to clear old, abandoned guest carts (e.g., older than 30 days) to prevent database bloat?
> - Can unauthenticated (guest) users share carts across devices via a link, or is it strictly tied to their browser session? (The default plan ties it to a browser cookie/session).

## Proposed Changes

---

### Backend (Database & Models)

We will introduce two new tables and their corresponding Eloquent models.

#### [NEW] database/migrations/...\_create_carts_table.php

Creates a `carts` table with columns:

- `id`
- `user_id` (nullable)
- `session_id` (string, for guests)
- `branch_id`, `coupon_id`, `order_type`, `subtotal`, `total`
- Timestamps

#### [NEW] database/migrations/...\_create_cart_items_table.php

Creates a `cart_items` table matching the existing `OrderItem` schema structure:

- `id`, `cart_id`, `item_id`
- `itemable_type`, `itemable_id` (supports Offers/Items)
- `quantity`, `price`, `discount`
- `item_variations` (json), `item_extras` (json)
- `item_variation_total`, `item_extra_total`, `total_price`
- Timestamps

#### [NEW] app/Models/Cart.php

#### [NEW] app/Models/CartItem.php

Defines the relationships (`$cart->items()`, `$cartItem->item()`, etc.).

---

### Backend (Controllers & Routes)

#### [NEW] app/Http/Controllers/Frontend/CartController.php

Handles API logic:

- `index()`: Fetch current cart via `user_id` or `session_id`.
- `add()`: Add new item or increment quantity.
- `update()`: Change quantity of existing items.
- `remove()` / `clear()`: Delete items from cart.
- `applyCoupon()`, `updateOrderType()`.

#### [MODIFY] routes/api.php

Add the endpoints for the new `CartController` under an appropriate `frontend` grouping.

#### [MODIFY] app/Http/Controllers/Auth/LoginController.php (or similar)

## Update the login logic to check if the user has a guest cart (`session_id`). If so, assign the guest cart to their `user_id` upon successful login, overriding or merging with any existing saved cart.

### Frontend (Vuex & API)

#### [MODIFY] resources/js/store/modules/frontend/frontendCart.js

Refactor Vuex actions (`lists`, `quantity`, `resetCart`, `coupon`, etc.) to make Axios requests to the new Cart APIs instead of just mutating local state.

- **State Changes**: On receiving a successful API response, the store will commit the DB state to Vuex.
- **Session Management**: Ensure the Axios interceptor or API calls attach a generated `cart_session_id` header (or store it in cookies) for guest users.

#### [MODIFY] resources/js/app.js (or bootstrapper)

- Manage `cart_session_id` (generate a UUID if it doesn't exist and save it to a cookie/localStorage).
- Trigger an initial API call to fetch the DB cart on page load.

## Verification Plan

### Automated Tests

- If there are existing feature tests in `tests/Feature/`, run/create tests to verify guest cart creation, adding items, and assigning the cart to a user upon login.

### Manual Verification

1. Open the frontend as a Guest. Add an item with variations/extras. Verify it persists after refreshing the page.
2. Log in. Verify the cart is transferred to the authenticated account.
3. Open a different browser and log in. Verify the cart items are synced.
4. Apply a coupon and change order types to ensure state updates properly reflect in the DB and UI.
