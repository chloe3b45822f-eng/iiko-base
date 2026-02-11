@extends('layouts.admin')

@section('title', 'Вебхуки и Заказы')
@section('page-title', 'Управление Вебхуками и Заказами')

@section('styles')
<style>
    .section-gap { margin-bottom: 20px; }
    
    /* Webhook Event Cards */
    .webhook-card {
        padding: 14px;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: rgba(255,255,255,0.03);
        margin-bottom: 8px;
        transition: all .15s;
    }
    .webhook-card:hover { background: rgba(255,255,255,0.06); transform: translateY(-1px); }
    .webhook-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .webhook-type {
        font-weight: 700;
        font-size: 14px;
        color: var(--accent-light);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .webhook-status {
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .webhook-processed { background: rgba(34,197,94,0.15); color: var(--success); }
    .webhook-failed { background: rgba(239,68,68,0.15); color: var(--danger); }
    .webhook-pending { background: rgba(245,158,11,0.15); color: var(--warning); }
    
    .webhook-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 8px;
        font-size: 12px;
        margin-top: 8px;
    }
    .webhook-detail-label {
        color: var(--muted);
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .webhook-detail-value {
        color: var(--text);
        font-weight: 500;
        word-break: break-all;
    }
    
    /* Enhanced Order Cards */
    .order-card-enhanced {
        padding: 16px;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: rgba(255,255,255,0.03);
        margin-bottom: 12px;
        transition: all .15s;
    }
    .order-card-enhanced:hover {
        background: rgba(255,255,255,0.06);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    
    .order-main-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .order-title-block {
        flex: 1;
        min-width: 200px;
    }
    .order-number {
        font-weight: 700;
        font-size: 18px;
        color: var(--text-bright);
        margin-bottom: 4px;
    }
    .order-external-id {
        font-size: 11px;
        color: var(--muted);
        font-family: 'SF Mono', monospace;
    }
    
    .order-actions {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }
    .order-action-btn {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid var(--border);
        background: rgba(255,255,255,0.05);
        color: var(--text);
        cursor: pointer;
        transition: all .15s;
    }
    .order-action-btn:hover {
        background: rgba(99,102,241,0.2);
        border-color: var(--accent);
        color: var(--accent-light);
    }
    
    .order-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
        margin-bottom: 12px;
    }
    .order-field {
        background: rgba(0,0,0,0.2);
        padding: 10px;
        border-radius: 8px;
    }
    .order-field-label {
        color: var(--muted);
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 4px;
    }
    .order-field-value {
        color: var(--text-bright);
        font-weight: 600;
        font-size: 13px;
    }
    .order-field-value.large {
        font-size: 16px;
        color: var(--accent-light);
    }
    
    .status-pill {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .status-unconfirmed { background: rgba(148,163,184,0.15); color: var(--muted); }
    .status-waitcooking { background: rgba(245,158,11,0.15); color: var(--warning); }
    .status-cookingstarted { background: rgba(245,158,11,0.2); color: var(--warning); }
    .status-cookingcompleted { background: rgba(34,197,94,0.15); color: var(--success); }
    .status-waiting { background: rgba(99,102,241,0.15); color: var(--accent-light); }
    .status-onway { background: rgba(34,211,238,0.15); color: var(--accent-2); }
    .status-delivered { background: rgba(34,197,94,0.2); color: var(--success); }
    .status-closed { background: rgba(148,163,184,0.12); color: var(--muted); }
    .status-cancelled { background: rgba(239,68,68,0.15); color: var(--danger); }
    
    .courier-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: rgba(34,211,238,0.1);
        border-radius: 8px;
        font-weight: 600;
        font-size: 12px;
        color: var(--accent-2);
    }
    
    .filter-bar {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 16px;
        align-items: center;
    }
    
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.8);
        z-index: 10000;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .modal.active { display: flex; }
    .modal-content {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 24px;
        max-width: 600px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        border: 1px solid var(--border);
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .modal-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-bright);
    }
    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: var(--muted);
        cursor: pointer;
        padding: 4px 8px;
    }
    .modal-close:hover { color: var(--text-bright); }
    
    .json-viewer {
        background: rgba(0,0,0,0.3);
        padding: 14px;
        border-radius: 8px;
        font-family: 'SF Mono', 'Fira Code', monospace;
        font-size: 11px;
        max-height: 400px;
        overflow: auto;
        white-space: pre-wrap;
        word-break: break-all;
    }
    
    .courier-assign-form {
        display: grid;
        gap: 12px;
    }
    
    .stat-mini {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        background: rgba(99,102,241,0.1);
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
{{-- Tab Bar --}}
<div class="tab-bar">
    <button class="tab-btn active" onclick="switchTab('orders', event)">📦 Заказы</button>
    <button class="tab-btn" onclick="switchTab('webhooks', event)">🔗 История Вебхуков</button>
    <button class="tab-btn" onclick="switchTab('couriers', event)">🚗 Курьеры</button>
    <button class="tab-btn" onclick="switchTab('bonuses', event)">🎁 Бонусы</button>
</div>

{{-- ═══ TAB: Enhanced Orders ═══ --}}
<div class="tab-content active" id="tab-orders">
    <div class="card section-gap">
        <div class="card-header">
            <div>
                <div class="card-title">Управление Заказами</div>
                <div class="card-subtitle">Полная информация о заказах с вебхуков и возможностью управления</div>
            </div>
            <button class="btn btn-primary btn-sm" onclick="loadEnhancedOrders()">🔄 Обновить</button>
        </div>
        
        <div class="filter-bar">
            <select class="form-input" id="order-status-filter-enhanced" style="max-width:200px;" onchange="loadEnhancedOrders()">
                <option value="">Все статусы</option>
                <option value="Unconfirmed">Не подтвержден</option>
                <option value="WaitCooking">Ожидает готовки</option>
                <option value="CookingStarted">Готовится</option>
                <option value="CookingCompleted">Приготовлен</option>
                <option value="Waiting">Ожидает</option>
                <option value="OnWay">В пути</option>
                <option value="Delivered">Доставлен</option>
                <option value="Closed">Закрыт</option>
                <option value="Cancelled">Отменен</option>
            </select>
            
            <select class="form-input" id="order-type-filter" style="max-width:150px;" onchange="loadEnhancedOrders()">
                <option value="">Все типы</option>
                <option value="DELIVERY">Доставка</option>
                <option value="PICKUP">Самовывоз</option>
                <option value="DINE_IN">В зале</option>
            </select>
            
            <input type="text" class="form-input" id="order-search" placeholder="Поиск по номеру, телефону..." style="max-width:250px;" onkeyup="if(event.key==='Enter')loadEnhancedOrders()">
            <button class="btn btn-sm" onclick="loadEnhancedOrders()">🔍 Найти</button>
        </div>
        
        <div id="stats-row" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;">
            <span class="stat-mini">📊 Всего: <span id="stat-total-orders">0</span></span>
            <span class="stat-mini" style="background:rgba(34,211,238,0.1);">🚗 С курьером: <span id="stat-with-courier">0</span></span>
            <span class="stat-mini" style="background:rgba(245,158,11,0.1);">⏳ Активных: <span id="stat-active-orders">0</span></span>
        </div>
        
        <div id="enhanced-orders-list">
            <div class="loading-overlay"><span class="spinner"></span> Загрузка заказов...</div>
        </div>
    </div>
</div>

{{-- ═══ TAB: Webhook History ═══ --}}
<div class="tab-content" id="tab-webhooks">
    <div class="card section-gap">
        <div class="card-header">
            <div>
                <div class="card-title">История Входящих Вебхуков</div>
                <div class="card-subtitle">Все события, полученные от iiko в реальном времени</div>
            </div>
            <button class="btn btn-primary btn-sm" onclick="loadWebhookEvents()">🔄 Обновить</button>
        </div>
        
        <div class="filter-bar">
            <select class="form-input" id="webhook-type-filter" style="max-width:200px;" onchange="loadWebhookEvents()">
                <option value="">Все типы</option>
                <option value="CREATE">CREATE</option>
                <option value="UPDATE">UPDATE</option>
                <option value="DeliveryOrderUpdate">DeliveryOrderUpdate</option>
                <option value="DeliveryOrderError">DeliveryOrderError</option>
                <option value="StopListUpdate">StopListUpdate</option>
            </select>
            
            <select class="form-input" id="webhook-status-filter" style="max-width:150px;" onchange="loadWebhookEvents()">
                <option value="">Все</option>
                <option value="true">Обработано</option>
                <option value="false">Не обработано</option>
            </select>
            
            <input type="text" class="form-input" id="webhook-search" placeholder="Поиск по external_id..." style="max-width:250px;" onkeyup="if(event.key==='Enter')loadWebhookEvents()">
            <button class="btn btn-sm" onclick="loadWebhookEvents()">🔍 Найти</button>
        </div>
        
        <div id="webhook-stats" style="margin-bottom:16px;">
            <span class="stat-mini">📊 Всего: <span id="stat-total-webhooks">0</span></span>
            <span class="stat-mini" style="background:rgba(34,197,94,0.1);">✅ Обработано: <span id="stat-processed">0</span></span>
            <span class="stat-mini" style="background:rgba(239,68,68,0.1);">❌ Ошибок: <span id="stat-failed">0</span></span>
        </div>
        
        <div id="webhook-events-list">
            <div class="loading-overlay"><span class="spinner"></span> Загрузка событий...</div>
        </div>
    </div>
</div>

{{-- ═══ TAB: Couriers ═══ --}}
<div class="tab-content" id="tab-couriers">
    <div class="card section-gap">
        <div class="card-header">
            <div>
                <div class="card-title">Управление Курьерами</div>
                <div class="card-subtitle">Просмотр заказов по курьерам и назначение</div>
            </div>
            <button class="btn btn-primary btn-sm" onclick="loadCourierStats()">🔄 Обновить</button>
        </div>
        
        <div id="courier-stats-list">
            <div class="loading-overlay"><span class="spinner"></span> Загрузка данных курьеров...</div>
        </div>
    </div>
</div>

{{-- ═══ TAB: Bonuses ═══ --}}
<div class="tab-content" id="tab-bonuses">
    <div class="card section-gap">
        <div class="card-header">
            <div>
                <div class="card-title">История Бонусных Транзакций</div>
                <div class="card-subtitle">Начисления и списания бонусов</div>
            </div>
            <button class="btn btn-primary btn-sm" onclick="loadBonusTransactions()">🔄 Обновить</button>
        </div>
        
        <div class="filter-bar">
            <select class="form-input" id="bonus-type-filter" style="max-width:200px;" onchange="loadBonusTransactions()">
                <option value="">Все операции</option>
                <option value="topup">Начисление</option>
                <option value="withdraw">Списание</option>
                <option value="hold">Холдирование</option>
            </select>
            
            <input type="text" class="form-input" id="bonus-search" placeholder="Поиск по телефону, имени..." style="max-width:250px;" onkeyup="if(event.key==='Enter')loadBonusTransactions()">
            <button class="btn btn-sm" onclick="loadBonusTransactions()">🔍 Найти</button>
        </div>
        
        <div id="bonus-transactions-list">
            <div class="loading-overlay"><span class="spinner"></span> Загрузка транзакций...</div>
        </div>
    </div>
</div>

{{-- Modals --}}
<div id="order-details-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Детали Заказа</div>
            <button class="modal-close" onclick="closeModal('order-details-modal')">×</button>
        </div>
        <div id="order-details-content"></div>
    </div>
</div>

<div id="courier-assign-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Назначить Курьера</div>
            <button class="modal-close" onclick="closeModal('courier-assign-modal')">×</button>
        </div>
        <div class="courier-assign-form">
            <input type="hidden" id="assign-order-id">
            <div class="form-group">
                <label class="form-label">ID Курьера</label>
                <input type="text" class="form-input" id="assign-courier-id" placeholder="UUID курьера">
            </div>
            <div class="form-group">
                <label class="form-label">Имя Курьера</label>
                <input type="text" class="form-input" id="assign-courier-name" placeholder="Иван Иванов">
            </div>
            <button class="btn btn-primary" onclick="submitCourierAssignment()">✅ Назначить</button>
        </div>
    </div>
</div>

<div id="status-change-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Изменить Статус</div>
            <button class="modal-close" onclick="closeModal('status-change-modal')">×</button>
        </div>
        <div class="courier-assign-form">
            <input type="hidden" id="status-order-id">
            <div class="form-group">
                <label class="form-label">Новый Статус</label>
                <select class="form-input" id="new-status-select">
                    <option value="WaitCooking">Ожидает готовки</option>
                    <option value="CookingStarted">Готовится</option>
                    <option value="CookingCompleted">Приготовлен</option>
                    <option value="Waiting">Ожидает</option>
                    <option value="OnWay">В пути</option>
                    <option value="Delivered">Доставлен</option>
                    <option value="Closed">Закрыт</option>
                    <option value="Cancelled">Отменен</option>
                </select>
            </div>
            <button class="btn btn-primary" onclick="submitStatusChange()">✅ Обновить</button>
        </div>
    </div>
</div>

<div id="webhook-details-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Детали Вебхука</div>
            <button class="modal-close" onclick="closeModal('webhook-details-modal')">×</button>
        </div>
        <div id="webhook-details-content"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// Tab switching
function switchTab(name, evt) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    if (evt && evt.target) evt.target.classList.add('active');
    
    // Auto-load data
    if (name === 'orders') loadEnhancedOrders();
    if (name === 'webhooks') loadWebhookEvents();
    if (name === 'couriers') loadCourierStats();
    if (name === 'bonuses') loadBonusTransactions();
}

// API helpers
async function apiGet(url) {
    const res = await fetch(url, {
        headers: { 'X-CSRF-TOKEN': csrfToken }
    });
    return res.json();
}

async function apiPost(url, body = {}) {
    const res = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(body),
    });
    return { status: res.status, data: await res.json() };
}

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = String(str);
    return div.innerHTML;
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    return d.toLocaleDateString('ru-RU') + ' ' + d.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
}

function getStatusClass(status) {
    const s = (status || '').toLowerCase();
    return 'status-' + s.replace('_', '');
}

function getStatusLabel(status) {
    const map = {
        'Unconfirmed': 'Не подтвержден',
        'WaitCooking': 'Ожидает готовки',
        'ReadyForCooking': 'Готов к готовке',
        'CookingStarted': 'Готовится',
        'CookingCompleted': 'Приготовлен',
        'Waiting': 'Ожидает',
        'OnWay': 'В пути',
        'Delivered': 'Доставлен',
        'Closed': 'Закрыт',
        'Cancelled': 'Отменен',
    };
    return map[status] || status || '—';
}

// Modal functions
function openModal(modalId) {
    document.getElementById(modalId).classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// ─── Enhanced Orders ────────────────────────────────────────
async function loadEnhancedOrders() {
    const container = document.getElementById('enhanced-orders-list');
    const statusFilter = document.getElementById('order-status-filter-enhanced').value;
    const typeFilter = document.getElementById('order-type-filter').value;
    const search = document.getElementById('order-search').value;
    
    container.innerHTML = '<div class="loading-overlay"><span class="spinner"></span> Загрузка...</div>';
    
    try {
        let url = '/admin/api/orders?limit=100';
        if (statusFilter) url += '&status=' + encodeURIComponent(statusFilter);
        if (typeFilter) url += '&order_type=' + encodeURIComponent(typeFilter);
        if (search) url += '&search=' + encodeURIComponent(search);
        
        const orders = await apiGet(url);
        const ordersList = Array.isArray(orders) ? orders : [];
        
        // Update stats
        document.getElementById('stat-total-orders').textContent = ordersList.length;
        document.getElementById('stat-with-courier').textContent = ordersList.filter(o => o.courier_id).length;
        document.getElementById('stat-active-orders').textContent = ordersList.filter(o => !['Closed', 'Cancelled', 'Delivered'].includes(o.status)).length;
        
        if (ordersList.length === 0) {
            container.innerHTML = '<span class="badge badge-muted">Заказов не найдено</span>';
            return;
        }
        
        let html = '';
        ordersList.forEach(order => {
            const amount = ((order.total_amount || 0) / 100).toFixed(2);
            html += `
                <div class="order-card-enhanced">
                    <div class="order-main-header">
                        <div class="order-title-block">
                            <div class="order-number">
                                ${order.readable_number || '#' + order.id}
                                ${order.external_order_id ? '<span class="order-external-id">EXT: ' + escapeHtml(order.external_order_id) + '</span>' : ''}
                            </div>
                            <span class="status-pill ${getStatusClass(order.status)}">${getStatusLabel(order.status)}</span>
                        </div>
                        <div class="order-actions">
                            <button class="order-action-btn" onclick="viewOrderDetails(${order.id})" title="Детали">📋</button>
                            <button class="order-action-btn" onclick="openStatusChange(${order.id})" title="Изменить статус">🔄</button>
                            <button class="order-action-btn" onclick="openCourierAssign(${order.id})" title="Назначить курьера">🚗</button>
                            <button class="order-action-btn" onclick="cancelOrder(${order.id})" title="Отменить">❌</button>
                        </div>
                    </div>
                    
                    <div class="order-grid">
                        <div class="order-field">
                            <div class="order-field-label">Сумма</div>
                            <div class="order-field-value large">${amount} ₽</div>
                        </div>
                        ${order.courier_name ? `
                        <div class="order-field">
                            <div class="order-field-label">Курьер</div>
                            <div class="order-field-value">
                                <span class="courier-badge">🚗 ${escapeHtml(order.courier_name)}</span>
                            </div>
                        </div>
                        ` : ''}
                        ${order.order_type ? `
                        <div class="order-field">
                            <div class="order-field-label">Тип</div>
                            <div class="order-field-value">${escapeHtml(order.order_type)}</div>
                        </div>
                        ` : ''}
                        ${order.restaurant_name ? `
                        <div class="order-field">
                            <div class="order-field-label">Ресторан</div>
                            <div class="order-field-value">${escapeHtml(order.restaurant_name)}</div>
                        </div>
                        ` : ''}
                        ${order.promised_time ? `
                        <div class="order-field">
                            <div class="order-field-label">Обещанное время</div>
                            <div class="order-field-value">${formatDate(order.promised_time)}</div>
                        </div>
                        ` : ''}
                        <div class="order-field">
                            <div class="order-field-label">Создан</div>
                            <div class="order-field-value">${formatDate(order.created_at)}</div>
                        </div>
                        ${order.customer_name ? `
                        <div class="order-field">
                            <div class="order-field-label">Клиент</div>
                            <div class="order-field-value">${escapeHtml(order.customer_name)}</div>
                        </div>
                        ` : ''}
                        ${order.customer_phone ? `
                        <div class="order-field">
                            <div class="order-field-label">Телефон</div>
                            <div class="order-field-value">${escapeHtml(order.customer_phone)}</div>
                        </div>
                        ` : ''}
                    </div>
                    
                    ${order.problem ? `
                    <div style="padding:10px;background:rgba(239,68,68,0.1);border-radius:8px;border:1px solid var(--danger);margin-top:8px;">
                        <div style="font-size:11px;color:var(--danger);font-weight:700;margin-bottom:4px;">⚠️ ПРОБЛЕМА</div>
                        <div style="font-size:12px;color:var(--text);">${escapeHtml(order.problem)}</div>
                    </div>
                    ` : ''}
                </div>
            `;
        });
        
        container.innerHTML = html;
    } catch (err) {
        container.innerHTML = `<div class="alert alert-danger">⚠️ Ошибка загрузки: ${escapeHtml(err.message)}</div>`;
    }
}

async function viewOrderDetails(orderId) {
    try {
        const order = await apiGet(`/admin/api/orders/${orderId}`);
        const content = document.getElementById('order-details-content');
        
        let html = '<div class="json-viewer">' + JSON.stringify(order, null, 2) + '</div>';
        content.innerHTML = html;
        openModal('order-details-modal');
    } catch (err) {
        alert('Ошибка загрузки деталей: ' + err.message);
    }
}

function openCourierAssign(orderId) {
    document.getElementById('assign-order-id').value = orderId;
    document.getElementById('assign-courier-id').value = '';
    document.getElementById('assign-courier-name').value = '';
    openModal('courier-assign-modal');
}

async function submitCourierAssignment() {
    const orderId = document.getElementById('assign-order-id').value;
    const courierId = document.getElementById('assign-courier-id').value;
    const courierName = document.getElementById('assign-courier-name').value;
    
    if (!courierId || !courierName) {
        alert('Заполните все поля');
        return;
    }
    
    try {
        const result = await apiPost(`/admin/api/orders/${orderId}/assign-courier`, {
            courier_id: courierId,
            courier_name: courierName
        });
        
        if (result.status === 200) {
            alert('✅ Курьер успешно назначен!');
            closeModal('courier-assign-modal');
            loadEnhancedOrders();
        } else {
            alert('❌ Ошибка: ' + JSON.stringify(result.data));
        }
    } catch (err) {
        alert('❌ Ошибка: ' + err.message);
    }
}

function openStatusChange(orderId) {
    document.getElementById('status-order-id').value = orderId;
    openModal('status-change-modal');
}

async function submitStatusChange() {
    const orderId = document.getElementById('status-order-id').value;
    const newStatus = document.getElementById('new-status-select').value;
    
    try {
        const result = await apiPost(`/admin/api/orders/${orderId}/update-status`, {
            status: newStatus
        });
        
        if (result.status === 200) {
            alert('✅ Статус успешно обновлен!');
            closeModal('status-change-modal');
            loadEnhancedOrders();
        } else {
            alert('❌ Ошибка: ' + JSON.stringify(result.data));
        }
    } catch (err) {
        alert('❌ Ошибка: ' + err.message);
    }
}

async function cancelOrder(orderId) {
    if (!confirm('Вы уверены, что хотите отменить заказ #' + orderId + '?')) return;
    
    const reason = prompt('Укажите причину отмены:');
    if (!reason) return;
    
    try {
        const result = await apiPost(`/admin/api/orders/${orderId}/cancel`, {
            cancel_reason: reason
        });
        
        if (result.status === 200) {
            alert('✅ Заказ отменен!');
            loadEnhancedOrders();
        } else {
            alert('❌ Ошибка: ' + JSON.stringify(result.data));
        }
    } catch (err) {
        alert('❌ Ошибка: ' + err.message);
    }
}

// ─── Webhook Events ─────────────────────────────────────────
async function loadWebhookEvents() {
    const container = document.getElementById('webhook-events-list');
    const typeFilter = document.getElementById('webhook-type-filter').value;
    const statusFilter = document.getElementById('webhook-status-filter').value;
    const search = document.getElementById('webhook-search').value;
    
    container.innerHTML = '<div class="loading-overlay"><span class="spinner"></span> Загрузка...</div>';
    
    try {
        let url = '/admin/api/webhooks/events?limit=100';
        if (typeFilter) url += '&event_type=' + encodeURIComponent(typeFilter);
        if (statusFilter) url += '&processed=' + encodeURIComponent(statusFilter);
        if (search) url += '&search=' + encodeURIComponent(search);
        
        const events = await apiGet(url);
        const eventsList = Array.isArray(events) ? events : [];
        
        // Update stats
        document.getElementById('stat-total-webhooks').textContent = eventsList.length;
        document.getElementById('stat-processed').textContent = eventsList.filter(e => e.processed).length;
        document.getElementById('stat-failed').textContent = eventsList.filter(e => e.processing_error).length;
        
        if (eventsList.length === 0) {
            container.innerHTML = '<span class="badge badge-muted">События не найдены</span>';
            return;
        }
        
        let html = '';
        eventsList.forEach(event => {
            const statusClass = event.processing_error ? 'webhook-failed' : (event.processed ? 'webhook-processed' : 'webhook-pending');
            const statusText = event.processing_error ? '❌ Ошибка' : (event.processed ? '✅ Обработано' : '⏳ Ожидает');
            
            html += `
                <div class="webhook-card" onclick="viewWebhookDetails(${event.id})">
                    <div class="webhook-header">
                        <div class="webhook-type">📡 ${escapeHtml(event.event_type)}</div>
                        <span class="webhook-status ${statusClass}">${statusText}</span>
                    </div>
                    <div class="webhook-details">
                        ${event.order_external_id ? `
                        <div>
                            <div class="webhook-detail-label">External ID</div>
                            <div class="webhook-detail-value">${escapeHtml(event.order_external_id)}</div>
                        </div>
                        ` : ''}
                        ${event.organization_id ? `
                        <div>
                            <div class="webhook-detail-label">Organization ID</div>
                            <div class="webhook-detail-value">${escapeHtml(event.organization_id).substring(0, 12)}...</div>
                        </div>
                        ` : ''}
                        <div>
                            <div class="webhook-detail-label">Получено</div>
                            <div class="webhook-detail-value">${formatDate(event.created_at)}</div>
                        </div>
                        ${event.processing_error ? `
                        <div>
                            <div class="webhook-detail-label">Ошибка</div>
                            <div class="webhook-detail-value" style="color:var(--danger);">${escapeHtml(event.processing_error).substring(0, 50)}...</div>
                        </div>
                        ` : ''}
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    } catch (err) {
        container.innerHTML = `<div class="alert alert-danger">⚠️ Ошибка загрузки: ${escapeHtml(err.message)}</div>`;
    }
}

async function viewWebhookDetails(eventId) {
    try {
        const events = await apiGet('/admin/api/webhooks/events?limit=100');
        const event = events.find(e => e.id === eventId);
        if (!event) {
            alert('Событие не найдено');
            return;
        }
        
        const content = document.getElementById('webhook-details-content');
        let payload = {};
        try {
            payload = JSON.parse(event.payload);
        } catch {
            payload = { raw: event.payload };
        }
        
        let html = `
            <div style="margin-bottom:16px;">
                <strong>ID:</strong> ${event.id}<br>
                <strong>Тип:</strong> ${escapeHtml(event.event_type)}<br>
                <strong>Статус:</strong> ${event.processed ? '✅ Обработано' : '⏳ Ожидает'}<br>
                <strong>Время:</strong> ${formatDate(event.created_at)}
            </div>
            <div class="json-viewer">${JSON.stringify(payload, null, 2)}</div>
        `;
        
        content.innerHTML = html;
        openModal('webhook-details-modal');
    } catch (err) {
        alert('Ошибка загрузки деталей: ' + err.message);
    }
}

// ─── Couriers ───────────────────────────────────────────────
async function loadCourierStats() {
    const container = document.getElementById('courier-stats-list');
    container.innerHTML = '<div class="loading-overlay"><span class="spinner"></span> Загрузка...</div>';
    
    try {
        const orders = await apiGet('/admin/api/orders?limit=1000');
        const ordersList = Array.isArray(orders) ? orders : [];
        
        // Group by courier
        const courierMap = {};
        ordersList.forEach(order => {
            if (order.courier_id || order.courier_name) {
                const key = order.courier_id || order.courier_name;
                if (!courierMap[key]) {
                    courierMap[key] = {
                        id: order.courier_id,
                        name: order.courier_name || 'Неизвестно',
                        orders: []
                    };
                }
                courierMap[key].orders.push(order);
            }
        });
        
        const couriers = Object.values(courierMap);
        
        if (couriers.length === 0) {
            container.innerHTML = '<span class="badge badge-muted">Нет заказов с назначенными курьерами</span>';
            return;
        }
        
        let html = '';
        couriers.forEach(courier => {
            const activeOrders = courier.orders.filter(o => !['Closed', 'Cancelled', 'Delivered'].includes(o.status));
            const totalAmount = courier.orders.reduce((sum, o) => sum + (o.total_amount || 0), 0) / 100;
            
            html += `
                <div class="card" style="margin-bottom:12px;">
                    <div style="padding:16px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                            <div>
                                <div style="font-size:16px;font-weight:700;color:var(--text-bright);">🚗 ${escapeHtml(courier.name)}</div>
                                ${courier.id ? '<div style="font-size:11px;color:var(--muted);font-family:monospace;">ID: ' + escapeHtml(courier.id).substring(0,20) + '...</div>' : ''}
                            </div>
                            <div style="text-align:right;">
                                <div class="stat-mini">📦 ${courier.orders.length} заказов</div>
                                <div class="stat-mini" style="background:rgba(245,158,11,0.1);margin-top:4px;">⏳ ${activeOrders.length} активных</div>
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:8px;">
                            <div class="order-field">
                                <div class="order-field-label">Общая сумма</div>
                                <div class="order-field-value large">${totalAmount.toFixed(2)} ₽</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    } catch (err) {
        container.innerHTML = `<div class="alert alert-danger">⚠️ Ошибка загрузки: ${escapeHtml(err.message)}</div>`;
    }
}

// ─── Bonuses ────────────────────────────────────────────────
async function loadBonusTransactions() {
    const container = document.getElementById('bonus-transactions-list');
    const typeFilter = document.getElementById('bonus-type-filter').value;
    const search = document.getElementById('bonus-search').value;
    
    container.innerHTML = '<div class="loading-overlay"><span class="spinner"></span> Загрузка...</div>';
    
    try {
        let url = '/admin/api/loyalty/transactions?limit=100';
        if (typeFilter) url += '&operation_type=' + encodeURIComponent(typeFilter);
        if (search) url += '&search=' + encodeURIComponent(search);
        
        const transactions = await apiGet(url);
        const txList = Array.isArray(transactions) ? transactions : [];
        
        if (txList.length === 0) {
            container.innerHTML = '<span class="badge badge-muted">Транзакций не найдено</span>';
            return;
        }
        
        let html = '';
        txList.forEach(tx => {
            const typeLabel = tx.operation_type === 'topup' ? '➕ Начисление' :
                             tx.operation_type === 'withdraw' ? '➖ Списание' :
                             '🔒 Холд';
            const typeClass = tx.operation_type === 'topup' ? 'status-delivered' :
                             tx.operation_type === 'withdraw' ? 'status-cancelled' :
                             'status-warning';
            
            html += `
                <div class="webhook-card">
                    <div class="webhook-header">
                        <div class="webhook-type">${typeLabel}</div>
                        <span class="status-pill ${typeClass}">${tx.amount} бонусов</span>
                    </div>
                    <div class="webhook-details">
                        ${tx.customer_name ? `
                        <div>
                            <div class="webhook-detail-label">Клиент</div>
                            <div class="webhook-detail-value">${escapeHtml(tx.customer_name)}</div>
                        </div>
                        ` : ''}
                        ${tx.customer_phone ? `
                        <div>
                            <div class="webhook-detail-label">Телефон</div>
                            <div class="webhook-detail-value">${escapeHtml(tx.customer_phone)}</div>
                        </div>
                        ` : ''}
                        ${tx.comment ? `
                        <div>
                            <div class="webhook-detail-label">Комментарий</div>
                            <div class="webhook-detail-value">${escapeHtml(tx.comment)}</div>
                        </div>
                        ` : ''}
                        <div>
                            <div class="webhook-detail-label">Дата</div>
                            <div class="webhook-detail-value">${formatDate(tx.created_at)}</div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    } catch (err) {
        container.innerHTML = `<div class="alert alert-danger">⚠️ Ошибка загрузки: ${escapeHtml(err.message)}</div>`;
    }
}

// Auto-load on page load
document.addEventListener('DOMContentLoaded', () => {
    loadEnhancedOrders();
});
</script>
@endsection
