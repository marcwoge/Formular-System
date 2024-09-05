# Formular-System
## Description
The Formular-System is a PHP-based application designed to facilitate the creation and management of web forms. The system supports a variety of HTML5 form elements, multi-step forms, and advanced configurations, making it suitable for a wide range of use cases, including visitor registration, feedback collection, and more. It is easy to customize and integrate with other systems, such as MediaWiki, through extensions.

## Installation
### Requirements
- PHP: Version 7.3 or higher
- Web Server: Apache, Nginx, or any server that supports PHP

### Steps
1. Clone the Repository 
```
git clone https://github.com/marcwoge/Formular-System.git
cd Formular-System
```
2. Configure the Application

- Google API Configuration:

    - Rename config/googleapi_default.php to googleapi.php.
    - Set your Google Maps API key in the api_key field.
```
return [
    'api_key' => 'YOUR_GOOGLE_MAPS_API_KEY'
];
```
- Mail Configuration:

    - Rename config/mailconfig_default_.php to mailconfig.php.
    - Configure your SMTP settings for email sending.
```
return [
    'host' => 'your_smtp_server',
    'SMTPAuth' => true,
    'username' => 'your_username',
    'password' => 'your_password',
    'port' => 587,
    'encryption' => 'tls',  // or 'ssl'
    'from' => 'your_email@example.com',
    'SMTPOptions' => [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ],

        'defaultSubject' => 'Neue Formularübermittlung',
        'defaultRecipients' => ['mail@example.com'],
        'defaultPreText' => 'Hallo,<br>vielen Dank für Ihre Nachricht.<br>Folgende Daten wurden in unserem System gespeichert:<p>',
        'defaultPostText' => '</p> <br>Mit freundlichen Grüßen<br>Ihr Team',
    
// Server URL, an den die Daten gesendet werden
'server_url' => 'https://SERVER/request',
];
```
- Text Configuration:

    - Rename config/texts_default.php to texts.php.
    - Customize the disclaimer or any other text-based content used across the application.
```
return [
    'disclaimer' => '&copy; 2024 Marc-Philipp Woge',
    'company_name' => 'Formular-Server',
    'footer_text' => 'Mit freundlichen Grüßen, Ihr Team',
    // Weitere Texte hier hinzufügen...
];
```
- Logo Configuration:

    - Place your logo in the img directory with the filename logo.png.

3. Deploy to Web Server

- Upload the Formular-System directory to your web server.
- Ensure the web server has write permissions for the `uploads` and `form_submissions` directory. Be carefull with file permissions. 

## Form Validation
The system uses HTML5 form validation, along with custom validation scripts, to ensure that all required fields are filled out correctly before the form can be submitted.

## Customization
You can customize the look and feel of the forms and the system by editing the CSS and JS files:

- CSS: `styles.css`
- JavaScript: `menu.js`, `multi-step.js`, `validation.js`

## Usage
All forms are stored in the folderr `forms/` you can create new by adding a file xyzform.php. This file only needs the html of the form, all other html stuff is put together automatically. 
```
<form action="process_form.php" method="POST" class="validated-form">
    your Form here
</form>
```
### File `forms/start.php`
The file `forms/start.php` contains the welcome page alias Startpage when you visit the index.php without any parameters.
### Form Directory Structure
Forms are stored in the forms directory. Each form is a PHP file that contains only the HTML structure for the form. For example:

- `forms/form1.php`
- `forms/demo/form2.php`
These forms can be accessed by specifying the page parameter in the URL. For example:

- `https://www.yourdomain.com/index.php?page=form1.php&nav=true`
- `https://www.yourdomain.com/index.php?page=demo/form2.php&nav=wiki`

### Navigation Parameters
- `nav=true`: Shows the full layout, including navigation and header.
- `nav=wiki`: Displays only the form without header, footer, or navigation—suitable for embedding in MediaWiki or similar platforms.
- nav not set: Shows the layout but hides the navigation.

### Hidden Fields in Forms
Hidden fields allow you to customize the behavior and appearance of each form:

```
<input type="hidden" name="form_title" value="Form Title">
<input type="hidden" name="email_subject" value="Subject of the Email">
<input type="hidden" name="email_recipients" value="recipient1@example.com,recipient2@example.com">
<input type="hidden" name="email_pretext" value="Hello,<br>Thank you for registering. Here are the details you provided:<br><br>">
<input type="hidden" name="email_posttext" value="<br><br>Best regards,<br>Your team.">
<input type="hidden" name="file_attachments[]" value="attachment1.pdf">
<input type="hidden" name="file_attachments[]" value="attachment2.jpg">
```

- `form_title`: Title of the form, used in logs and emails.
- `email_subject`: Subject line of the email sent after form submission.
- `email_recipients`: Comma-separated list of email addresses to send the form data to.
- `email_pretext`: Text to appear at the beginning of the email body.
- `email_posttext`: Text to appear at the end of the email body.
- `file_attachments[]`: List of file paths to include as attachments in the email.

## Example Forms
### Simple Form Example
```
<form action="process_form.php" method="POST" class="validated-form">
    <input type="hidden" name="form_title" value="Simple Form">
    <input type="hidden" name="email_subject" value="Simple Form Submission">
    <input type="hidden" name="email_recipients" value="admin@example.com">

    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>

    <button type="submit">Submit</button>
</form>
```
### Multi-Step Form Example
```
<form id="multiStepForm" action="process_form.php" method="POST" class="validated-form">
    <input type="hidden" name="form_title" value="Multi-Step Form">
    <input type="hidden" name="email_subject" value="Multi-Step Form Submission">
    <input type="hidden" name="email_recipients" value="admin@example.com">

    <!-- Step 1 -->
    <div class="form-step">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <button type="button" class="next-step">Next</button>
    </div>

    <!-- Step 2 -->
    <div class="form-step" style="display:none;">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <button type="button" class="previous-step">Back</button>
        <button type="submit" class="submit">Submit</button>
    </div>
</form>
```
- JavaScript handles the transitions between steps.
- The class validated-form ensures that the form is validated before submission.

## Overview of HTML5 Form Elements
### Hidden Fields
Hidden fields are used to store data that is not visible to the user but is sent when the form is submitted. This is  sent by mail and provided to the http-call

- #### Form Title

```
<input type="hidden" name="form_title" value="Formular 2">
```
Stores the title of the form.
- #### Email Subject
```
<input type="hidden" name="email_subject" value="Neue Formularübermittlung">
```
Defines the subject of the email that will be sent upon form submission.
- #### Email Recipients
```
<input type="hidden" name="email_recipients" value="email@example.com, email2@example.com"> 
```
Specifies the recipients of the email. Multiple recipients can be separated by commas.
- #### Email Pretext
```
<input type="hidden" name="email_pretext" value="Hallo,<br>Vielen Dank für Ihre Registrierung.<br>Folgende Daten wurden in unserem System gespeichert:<p>">
```
Adds introductory text to the email body.
- #### Email Posttext
```
<input type="hidden" name="email_posttext" value="</p> <br>Wir wünschen Ihnen einen tollen Aufenthalt.<br>Mit freundlichen Grüßen<br> Türsteher.">
```
Adds closing text to the email body.
- #### File Attachments
```
<input type="hidden" name="file_attachments[]" value="datei1.pdf">
<input type="hidden" name="file_attachments[]" value="datei2.jpg">
```
Attaches files to the email. Multiple files can be specified by using multiple hidden fields with the same name.

### Visible Form Elements
These are standard HTML5 elements used to capture user input.

- #### Text Input
```
<label for="text">Text:</label>
<input type="text" id="text" name="text">
```
Single-line text input.
- #### Email Input
```
<label for="email">Email:</label>
<input type="email" id="email" name="email">
```
Email input, which validates the input as an email address.
- #### Password Input
```
<label for="password">Passwort:</label>
<input type="password" id="password" name="password">
```
Password input, hides the characters as they are typed.
- #### Number Input
```
<label for="number">Zahl:</label>
<input type="number" id="number" name="number" min="0" max="100">
```
Numeric input with defined minimum and maximum values.
- #### Range Slider
```
<label for="Slider">Slider: <span id="sliderValue">50</span></label>
<input type="range" min="1" max="100" value="50" class="slider" id="myRange" name="slider_value">
```
Slider for selecting a value within a range.
- #### Date Input
```
<label for="date">Datum:</label>
<input type="date" id="date" name="date">
```
Date input with a calendar picker.
- #### Time Input
```
<label for="time">Uhrzeit:</label>
<input type="time" id="time" name="time">
```
Time input with a time picker.
- #### Color Picker
```
<label for="color">Farbe:</label>
<input type="color" id="color" name="color" value="#003057">
```
Color picker for selecting a color.
- #### File Upload
```
<label for="file">Datei hochladen:</label>
<input type="file" id="file" name="file">
```
File input for uploading a file.
- #### Telephone Input
```
<label for="tel">Telefonnummer:</label>
<input type="tel" id="tel" name="tel">
```
Input for telephone numbers.
- #### URL Input
```
<label for="url">Website:</label>
<input type="url" id="url" name="url">
```
Input for URLs, validates as a proper web address.
- #### Text Area
```
<label for="textarea">Nachricht:</label>
<textarea id="textarea" name="textarea" rows="4" cols="50"></textarea>
```
Multi-line text input for longer messages.
- #### Radio Buttons
```
<label for="radio">Wählen Sie eine Option:</label><br>
<input type="radio" id="option1" name="radioOptions" value="Option 1">
<label for="option1">Option 1</label><br>
<input type="radio" id="option2" name="radioOptions" value="Option 2">
<label for="option2">Option 2</label><br>
<input type="radio" id="option3" name="radioOptions" value="Option 3">
<label for="option3">Option 3</label>
```
Select one option from a set of choices.
- #### Checkboxes
```
<label for="checkbox">Wählen Sie eine oder mehrere Optionen:</label><br>
<label for="checkbox1">Checkbox 1</label>
<input type="checkbox" id="checkbox1" name="checkboxes" value="Checkbox 1"><br>
<label for="checkbox2">Checkbox 2</label>
<input type="checkbox" id="checkbox2" name="checkboxes" value="Checkbox 2"><br>
<label for="checkbox3">Checkbox 3</label>
<input type="checkbox" id="checkbox3" name="checkboxes" value="Checkbox 3"><br>
```
Allows selecting one or more options from a set.
- #### Select Dropdown
```
<label for="select">Wählen Sie eine Option:</label>
<select id="select" name="select">
    <option value="Option 1">Option 1</option>
    <option value="Option 2">Option 2</option>
    <option value="Option 3">Option 3</option>
</select>
```
Dropdown menu for selecting one option.
- #### Datalist
```
<label for="datalist">Wählen Sie oder geben Sie einen Wert ein:</label>
<input list="datalistOptions" id="datalist" name="datalist">
<datalist id="datalistOptions">
    <option value="Wert 1">
    <option value="Wert 2">
    <option value="Wert 3">
</datalist>
```
Provides a combination of input text with suggested options.
- #### Output
```
<label for="output">Ergebnis:</label>
<output id="output" name="output">0</output>
```
Displays a result or output.
- ### Buttons
- #### Submit Button
```
<button type="submit">Absenden</button>
```
Submits the form.
- #### Previous and Next Step Buttons (for multi-step forms)
```
<button type="button" class="previous-step">Zurück</button>
<button type="button" class="next-step">Weiter</button>
```
Navigates between steps in a multi-step form.
- ### Additional Features
- #### Images
```
<img src="/forms/img/NTC_eShelter_59.jpg" alt="Beschreibung" class="img-responsive">
```
Displays an image.
- #### Videos
```
<video controls class="video-responsive">
    <source src="/forms/img/NTC_-_Animation_2023.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>
```
Embeds a video with controls.

## Form Submissions
All form submissions are saved in the form_submissions directory as JSON files. Each submission is stored in a separate file, named based on the timestamp of the submission. This allows for easy review and processing of submitted data.

# License
The software is licensed under the MIT License. Please refer to the LICENSE file for more details.

# Related Projects
[mwFormularSystem](https://github.com/marcwoge/mwFormularSystem): A MediaWiki extension that allows embedding forms from the Formular-System directly into MediaWiki pages.

# Disclaimer
Security Warning: This system is provided as-is, without any guarantees of security or reliability. It has not undergone extensive security testing and may contain vulnerabilities. If you plan to use this system in a production environment, it is strongly recommended to conduct thorough security reviews and testing. This software is intended für intranet use.

The developers of this system are not responsible for any damages or security breaches that may occur as a result of using this software. Use it at your own risk.
