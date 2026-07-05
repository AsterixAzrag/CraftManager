import './bootstrap';

const numberFormatter = new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

function formatNumber(value) {
    return numberFormatter.format(value);
}

function formatCurrency(value) {
    return `$${formatNumber(value)}`;
}

document.addEventListener('click', (event) => {
    const addButton = event.target.closest('[data-add-customization]');
    const removeButton = event.target.closest('[data-remove-customization]');
    const addOrderItemButton = event.target.closest('[data-add-order-item]');
    const removeOrderItemButton = event.target.closest('[data-remove-order-item]');

    if (addButton) {
        const section = addButton.closest('[data-product-customizations]');
        const list = section.querySelector('[data-customization-list]');
        const template = section.querySelector('[data-customization-template]');
        const emptyState = section.querySelector('[data-customization-empty]');
        const index = list.querySelectorAll('[data-customization-item]').length;
        const item = template.content.firstElementChild.cloneNode(true);

        item.querySelectorAll('[data-name]').forEach((field) => {
            field.name = `options[${index}][${field.dataset.name}]`;
            field.removeAttribute('data-name');
        });

        list.appendChild(item);
        updateCustomizationMaterialRow(item);
        emptyState.classList.add('hidden');
    }

    if (removeButton) {
        const section = removeButton.closest('[data-product-customizations]');
        const list = section.querySelector('[data-customization-list]');
        const emptyState = section.querySelector('[data-customization-empty]');

        removeButton.closest('[data-customization-item]').remove();

        list.querySelectorAll('[data-customization-item]').forEach((item, index) => {
            item.querySelectorAll('[name^="options["]').forEach((field) => {
                field.name = field.name.replace(/options\[\d+\]/, `options[${index}]`);
            });
        });

        emptyState.classList.toggle('hidden', list.querySelectorAll('[data-customization-item]').length > 0);
        updateProductPricing(section);
    }

    if (addOrderItemButton) {
        const section = addOrderItemButton.closest('[data-order-items-section]');
        const list = section.querySelector('[data-order-items-list]');
        const template = section.querySelector('[data-order-item-template]');
        const index = list.querySelectorAll('[data-order-item]').length;
        const wrapper = document.createElement('div');

        wrapper.innerHTML = template.innerHTML.replaceAll('__INDEX__', index);
        list.appendChild(wrapper.firstElementChild);
        syncOrderItemRemoveButtons(section);
        calculateOrderItemPrice(list.lastElementChild);
        updateOrderSummary(section);
    }

    if (removeOrderItemButton) {
        const section = removeOrderItemButton.closest('[data-order-items-section]');
        const list = section.querySelector('[data-order-items-list]');

        if (list.querySelectorAll('[data-order-item]').length <= 1) {
            return;
        }

        removeOrderItemButton.closest('[data-order-item]').remove();
        renumberOrderItems(section);
        syncOrderItemRemoveButtons(section);
        updateOrderSummary(section);
    }
});

document.addEventListener('change', (event) => {
    const materialSelect = event.target.closest('[data-customization-material-search], [data-customization-material]');

    if (materialSelect) {
        updateCustomizationMaterialRow(materialSelect.closest('[data-customization-item]'));
        return;
    }

    const productSelect = event.target.closest('[data-order-product-select]');

    if (!productSelect) {
        return;
    }

    const item = productSelect.closest('[data-order-item]');
    calculateOrderItemPrice(item);
    updateOrderSummary(item.closest('[data-order-items-section]'));
});

document.addEventListener('input', (event) => {
    const materialSearch = event.target.closest('[data-customization-material-search]');

    if (materialSearch) {
        updateCustomizationMaterialRow(materialSearch.closest('[data-customization-item]'));
        return;
    }

    const expensePercentageField = event.target.closest('[data-product-expense-percentage]');
    const suggestedAdjustmentField = event.target.closest('[data-suggested-price-adjustment]');

    if (expensePercentageField) {
        updateProductPricing(expensePercentageField.closest('[data-product-customizations]'));
        return;
    }

    if (suggestedAdjustmentField) {
        updateProductPricing(suggestedAdjustmentField.closest('[data-product-customizations]'));
        return;
    }

    const materialQuantity = event.target.closest('[data-customization-item] [data-customization-quantity]');

    if (materialQuantity) {
        updateCustomizationMaterialRow(materialQuantity.closest('[data-customization-item]'));
        return;
    }

    const field = event.target.closest('[data-customization-quantity], [data-order-quantity]');

    if (field) {
        calculateOrderItemPrice(field.closest('[data-order-item]'));
        updateOrderSummary(field.closest('[data-order-items-section]'));
        return;
    }

    const orderDiscount = event.target.closest('[data-order-discount]');

    if (orderDiscount) {
        updateOrderSummary(orderDiscount.closest('[data-order-items-section]'));
    }
});

function renumberOrderItems(section) {
    section.querySelectorAll('[data-order-item]').forEach((item, index) => {
        item.querySelectorAll('[name^="items["]').forEach((field) => {
            field.name = field.name.replace(/items\[\d+\]/, `items[${index}]`);
        });
    });
}

function syncOrderItemRemoveButtons(section) {
    const items = section.querySelectorAll('[data-order-item]');

    items.forEach((item) => {
        const removeButton = item.querySelector('[data-remove-order-item]');

        if (removeButton) {
            removeButton.classList.toggle('hidden', items.length <= 1);
        }
    });
}

document.querySelectorAll('[data-order-items-section]').forEach(syncOrderItemRemoveButtons);
document.querySelectorAll('[data-order-item]').forEach(calculateOrderItemPrice);
document.querySelectorAll('[data-order-items-section]').forEach(updateOrderSummary);
document.querySelectorAll('[data-customization-item]').forEach(updateCustomizationMaterialRow);
document.querySelectorAll('[data-product-customizations]').forEach(updateProductPricing);
document.querySelectorAll('[data-work-start], [data-work-end], [data-overtime-end]').forEach((field) => {
    field.addEventListener('input', () => updateWorkHours(field.closest('form')));
    updateWorkHours(field.closest('form'));
});

function updateCustomizationMaterialRow(row) {
    if (!row || row.closest('[data-order-item]')) {
        return;
    }

    const materialField = row.querySelector('[data-customization-material]');
    const categoryField = row.querySelector('[data-customization-category]');
    const unitCostField = row.querySelector('[data-customization-unit-cost]');
    const quantityField = row.querySelector('[data-customization-quantity]');
    const valueField = row.querySelector('[data-customization-value]');
    const selectedMaterial = selectedMaterialOption(row);
    const unitCost = Number.parseFloat(selectedMaterial?.dataset.unitCost || '0') || 0;
    const quantity = Number.parseFloat(quantityField?.value || '0') || 0;

    if (materialField) {
        materialField.value = selectedMaterial?.dataset.id || '';
    }

    if (categoryField) {
        categoryField.value = selectedMaterial
            ? selectedMaterial?.dataset.category || 'Sin categoria'
            : 'Sin categoria';
    }

    if (unitCostField) {
        unitCostField.value = unitCost.toFixed(2);
    }

    if (valueField) {
        valueField.value = (unitCost * quantity).toFixed(2);
    }

    updateProductPricing(row.closest('[data-product-customizations]'));
}

function selectedMaterialOption(row) {
    const searchField = row.querySelector('[data-customization-material-search]');
    const list = searchField?.list;

    if (searchField && list) {
        return [...list.options].find((option) => option.value === searchField.value) ?? null;
    }

    const materialField = row.querySelector('[data-customization-material]');

    return materialField?.selectedOptions?.[0] ?? null;
}

function updateProductPricing(section) {
    if (!section) {
        return;
    }

    const materialsTotal = [...section.querySelectorAll('[data-customization-value]')]
        .reduce((total, field) => total + (Number.parseFloat(field.value || '0') || 0), 0);
    const expensePercentageTotal = [...section.querySelectorAll('[data-product-expense-percentage]')]
        .reduce((total, field) => total + (Number.parseFloat(field.value || '0') || 0), 0);
    const profitAmount = materialsTotal * (expensePercentageTotal / 100);
    const adjustment = Number.parseFloat(section.querySelector('[data-suggested-price-adjustment]')?.value || '0') || 0;
    const subtotal = materialsTotal + profitAmount + adjustment;
    const inventoryCapacity = productInventoryCapacity(section);

    section.querySelector('[data-product-materials-total]').textContent = formatCurrency(materialsTotal);
    section.querySelector('[data-product-expense-percentage-total]').textContent = formatNumber(expensePercentageTotal);
    section.querySelector('[data-product-profit-total]').textContent = formatCurrency(profitAmount);
    section.querySelector('[data-product-subtotal]').textContent = formatCurrency(subtotal);
    section.querySelector('[data-product-inventory-capacity]').textContent = inventoryCapacity.toString();
}

function productInventoryCapacity(section) {
    const capacities = [...section.querySelectorAll('[data-customization-item]')]
        .map((row) => {
            const materialField = row.querySelector('[data-customization-material]');
            const selectedMaterial = selectedMaterialOption(row);
            const quantity = Number.parseFloat(row.querySelector('[data-customization-quantity]')?.value || '0') || 0;

            if (!selectedMaterial || quantity <= 0) {
                return null;
            }

            const currentStock = Number.parseFloat(selectedMaterial?.dataset.currentStock || '0') || 0;

            return Math.floor(currentStock / quantity);
        })
        .filter((capacity) => capacity !== null);

    return capacities.length ? Math.min(...capacities) : 0;
}

function calculateOrderItemPrice(item) {
    if (!item) {
        return;
    }

    const productSelect = item.querySelector('[data-order-product-select]');
    const unitPriceField = item.querySelector('[data-order-unit-price]');
    const selectedOption = productSelect?.selectedOptions[0];

    if (!productSelect?.value || !unitPriceField) {
        updateOrderProductMaterials(item);
        return;
    }

    let unitPrice = Number.parseFloat(selectedOption?.dataset.basePrice || '0') || 0;

    unitPriceField.value = unitPrice.toFixed(2);
    updateOrderProductMaterials(item);
}

function updateOrderProductMaterials(item) {
    const productSelect = item.querySelector('[data-order-product-select]');
    const selectedProductId = productSelect?.value;
    const panels = item.querySelectorAll('[data-order-product-materials-panel]');
    const emptyMessage = item.querySelector('[data-free-product-message]');
    const orderQuantity = Number.parseFloat(item.querySelector('[data-order-quantity]')?.value || '1') || 1;
    let visiblePanels = 0;

    panels.forEach((panel) => {
        const isActive = panel.dataset.productId === selectedProductId;

        panel.classList.toggle('hidden', !isActive);

        if (isActive) {
            visiblePanels += 1;
            panel.querySelectorAll('[data-required-total]').forEach((field) => {
                const requiredPerProduct = Number.parseFloat(field.dataset.requiredPerProduct || '0') || 0;

                field.textContent = formatNumber(requiredPerProduct * orderQuantity);
            });
        }
    });

    if (emptyMessage) {
        emptyMessage.classList.toggle('hidden', Boolean(selectedProductId) && visiblePanels > 0);
        emptyMessage.textContent = selectedProductId
            ? 'Este producto no tiene materiales configurados.'
            : 'Selecciona un producto para ver sus materiales.';
    }
}

function updateOrderSummary(section) {
    if (!section) {
        return;
    }

    let productsTotal = 0;
    let requiredHours = 0;
    let hasInsufficientInventory = false;

    section.querySelectorAll('[data-order-item]').forEach((item) => {
        const quantity = Number.parseFloat(item.querySelector('[data-order-quantity]')?.value || '0') || 0;
        const unitPrice = Number.parseFloat(item.querySelector('[data-order-unit-price]')?.value || '0') || 0;
        const selectedProduct = item.querySelector('[data-order-product-select]')?.selectedOptions[0];
        const productionMinutes = Number.parseInt(selectedProduct?.dataset.productionMinutes || '0', 10) || 0;

        productsTotal += unitPrice * quantity;
        requiredHours += productionMinutes * quantity;

        item.querySelectorAll('[data-order-product-materials-panel]:not(.hidden) [data-required-total]').forEach((field) => {
            const required = Number.parseFloat(field.textContent || '0') || 0;
            const stock = Number.parseFloat(field.closest('tr')?.querySelector('[data-current-stock]')?.dataset.currentStock || '0') || 0;

            if (required > stock) {
                hasInsufficientInventory = true;
            }
        });
    });

    const discount = Number.parseFloat(section.querySelector('[data-order-discount]')?.value || '0') || 0;
    const total = Math.max(productsTotal - discount, 0);
    const inventoryStatus = section.querySelector('[data-order-inventory-status]');

    section.querySelector('[data-order-products-total]').textContent = formatCurrency(productsTotal);
    section.querySelector('[data-order-total]').textContent = formatCurrency(total);
    section.querySelector('[data-order-hours]').textContent = formatDuration(requiredHours);

    if (inventoryStatus) {
        inventoryStatus.textContent = hasInsufficientInventory ? 'Insuficiente' : 'Suficiente';
        inventoryStatus.classList.toggle('text-red-700', hasInsufficientInventory);
        inventoryStatus.classList.toggle('text-emerald-800', !hasInsufficientInventory);
    }
}

function formatDuration(totalMinutes) {
    const roundedMinutes = Math.round(totalMinutes);
    const hours = Math.floor(roundedMinutes / 60);
    const minutes = roundedMinutes % 60;

    return `${hours} h ${minutes} min`;
}

function updateWorkHours(form) {
    if (!form) {
        return;
    }

    const start = form.querySelector('[data-work-start]')?.value;
    const end = form.querySelector('[data-work-end]')?.value;
    const overtimeEnd = form.querySelector('[data-overtime-end]')?.value;
    const target = form.querySelector('[data-work-hours]');
    const overtimeTarget = form.querySelector('[data-overtime-hours]');

    if (!start || !end || !target) {
        return;
    }

    target.textContent = formatDuration(minutesBetween(start, end));

    if (overtimeEnd && overtimeTarget) {
        overtimeTarget.textContent = formatDuration(minutesBetween(end, overtimeEnd));
    }
}

function minutesBetween(start, end) {
    const [startHours, startMinutes] = start.split(':').map(Number);
    const [endHours, endMinutes] = end.split(':').map(Number);
    let total = (endHours * 60 + endMinutes) - (startHours * 60 + startMinutes);

    if (total < 0) {
        total += 24 * 60;
    }

    return total;
}
