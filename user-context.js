const USER_CONTEXT_STORAGE_KEY = 'formSystemUserContextFallback';

document.addEventListener('DOMContentLoaded', () => {
    const detectedContext = window.formSystemUserContext || {};
    const storedContext = loadStoredUserContext();
    const effectiveContext = buildEffectiveUserContext(detectedContext, storedContext);

    document.querySelectorAll('.validated-form').forEach((form, formIndex) => {
        injectUserContextFields(form, effectiveContext, formIndex);
        prefillCommonFields(form, effectiveContext);
    });
});

function buildEffectiveUserContext(detectedContext, storedContext) {
    const labels = detectedContext.labels || {
        section_title: 'Benutzerangaben',
        display_name: 'Name',
        email: 'E-Mail-Adresse'
    };

    return {
        available: Boolean(detectedContext.available),
        labels,
        display_name: detectedContext.display_name || storedContext.display_name || '',
        username: detectedContext.username || storedContext.username || '',
        email: detectedContext.email || storedContext.email || '',
        readonly_display_name: Boolean(detectedContext.display_name),
        readonly_username: Boolean(detectedContext.username),
        readonly_email: Boolean(detectedContext.email),
    };
}

function injectUserContextFields(form, context, formIndex) {
    if (form.querySelector('[data-user-context-fields="true"]')) {
        return;
    }

    const wrapper = document.createElement('fieldset');
    wrapper.className = 'user-context-box';
    wrapper.dataset.userContextFields = 'true';

    const legend = document.createElement('legend');
    legend.textContent = context.available
        ? (context.labels.section_title || 'Angemeldeter Windows-Benutzer')
        : 'Benutzerangaben';
    wrapper.appendChild(legend);

    const hint = document.createElement('p');
    hint.className = 'user-context-hint';
    hint.textContent = context.available
        ? 'Automatisch erkannte Angaben. Leere Felder können bei Bedarf ergänzt werden.'
        : 'Windows-Benutzer konnte nicht automatisch erkannt werden. Bitte Angaben hier eintragen.';
    wrapper.appendChild(hint);

    wrapper.appendChild(createUserContextField({
        labelText: context.labels.display_name || 'Name',
        fieldName: 'current_windows_display_name',
        value: context.display_name,
        formIndex,
        readOnly: context.readonly_display_name,
        type: 'text',
    }));
    wrapper.appendChild(createUserContextField({
        labelText: context.labels.email || 'E-Mail-Adresse',
        fieldName: 'current_windows_email',
        value: context.email,
        formIndex,
        readOnly: context.readonly_email,
        type: 'email',
    }));

    bindFallbackPersistence(wrapper);

    const insertionTarget = Array.from(form.children).find((child) => child.tagName !== 'INPUT' || child.type !== 'hidden');
    if (insertionTarget) {
        form.insertBefore(wrapper, insertionTarget);
    } else {
        form.appendChild(wrapper);
    }
}

function createUserContextField({ labelText, fieldName, value, formIndex, readOnly, type }) {
    const container = document.createElement('div');

    const id = `${fieldName}_${formIndex}`;
    const label = document.createElement('label');
    label.htmlFor = id;
    label.textContent = labelText + ':';

    const input = document.createElement('input');
    input.type = type;
    input.id = id;
    input.name = fieldName;
    input.value = value || '';
    input.readOnly = readOnly;
    input.dataset.userContextInput = 'true';

    container.appendChild(label);
    container.appendChild(input);

    return container;
}

function bindFallbackPersistence(wrapper) {
    const editableFields = wrapper.querySelectorAll('[data-user-context-input="true"]:not([readonly])');
    editableFields.forEach((field) => {
        field.addEventListener('input', persistEditableUserContext);
        field.addEventListener('change', persistEditableUserContext);
    });
}

function persistEditableUserContext() {
    const currentData = loadStoredUserContext();
    const nextData = {
        display_name: readFirstEditableValue('current_windows_display_name', currentData.display_name),
        email: readFirstEditableValue('current_windows_email', currentData.email),
    };

    try {
        window.localStorage.setItem(USER_CONTEXT_STORAGE_KEY, JSON.stringify(nextData));
    } catch (error) {
        console.warn('User context could not be saved locally.', error);
    }
}

function readFirstEditableValue(fieldName, fallbackValue) {
    const fields = document.querySelectorAll(`[name="${fieldName}"]:not([readonly])`);
    for (const field of fields) {
        if (field.value) {
            return field.value;
        }
    }

    return fallbackValue || '';
}

function loadStoredUserContext() {
    try {
        const rawValue = window.localStorage.getItem(USER_CONTEXT_STORAGE_KEY);
        if (!rawValue) {
            return {};
        }

        const parsedValue = JSON.parse(rawValue);
        return typeof parsedValue === 'object' && parsedValue !== null ? parsedValue : {};
    } catch (error) {
        return {};
    }
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
