document.addEventListener('DOMContentLoaded', () => {
    const context = window.formSystemUserContext;
    if (!context || !context.available) {
        return;
    }

    document.querySelectorAll('.validated-form').forEach((form, formIndex) => {
        injectUserContextFields(form, context, formIndex);
        prefillCommonFields(form, context);
    });
});

function injectUserContextFields(form, context, formIndex) {
    if (form.querySelector('[data-user-context-fields="true"]')) {
        return;
    }

    const wrapper = document.createElement('fieldset');
    wrapper.className = 'user-context-box';
    wrapper.dataset.userContextFields = 'true';

    const legend = document.createElement('legend');
    legend.textContent = context.labels.section_title || 'Angemeldeter Windows-Benutzer';
    wrapper.appendChild(legend);

    wrapper.appendChild(createReadonlyField(
        context.labels.display_name || 'Benutzername',
        'current_windows_display_name',
        context.display_name || '',
        formIndex
    ));
    wrapper.appendChild(createReadonlyField(
        context.labels.username || 'Windows-Login',
        'current_windows_user',
        context.username || '',
        formIndex
    ));
    wrapper.appendChild(createReadonlyField(
        context.labels.email || 'E-Mail-Adresse',
        'current_windows_email',
        context.email || '',
        formIndex,
        'email'
    ));

    const insertionTarget = Array.from(form.children).find((child) => child.tagName !== 'INPUT' || child.type !== 'hidden');
    if (insertionTarget) {
        form.insertBefore(wrapper, insertionTarget);
    } else {
        form.appendChild(wrapper);
    }
}

function createReadonlyField(labelText, fieldName, value, formIndex, type = 'text') {
    const container = document.createElement('div');

    const id = `${fieldName}_${formIndex}`;
    const label = document.createElement('label');
    label.htmlFor = id;
    label.textContent = labelText + ':';

    const input = document.createElement('input');
    input.type = type;
    input.id = id;
    input.name = fieldName;
    input.value = value;
    input.readOnly = true;

    container.appendChild(label);
    container.appendChild(input);

    return container;
}

function prefillCommonFields(form, context) {
    fillFirstEmptyField(form, ['name'], context.display_name || context.username);
    fillFirstEmptyField(form, ['username'], context.username || context.display_name);
    fillFirstEmptyField(form, ['email'], context.email);
}

function fillFirstEmptyField(form, candidateNames, value) {
    if (!value) {
        return;
    }

    for (const name of candidateNames) {
        const field = form.querySelector(`[name="${name}"]`);
        if (field && !field.value) {
            field.value = value;
            return;
        }
    }
}
