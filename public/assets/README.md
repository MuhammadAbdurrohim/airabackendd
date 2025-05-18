# AdminLTE Assets

This directory contains AdminLTE template files for the admin panel.

## Setup Instructions

1. Download AdminLTE 3.x from https://github.com/ColorlibHQ/AdminLTE/releases
2. Extract the following directories here:
   - `dist/` - Core CSS and JavaScript files
   - `plugins/` - Third-party plugins
   - `img/` - Default images and icons

## Directory Structure

```
assets/
├── dist/
│   ├── css/
│   ├── js/
│   └── img/
├── plugins/
│   ├── jquery/
│   ├── bootstrap/
│   ├── fontawesome-free/
│   └── ...
└── README.md
```

## Usage

Include the required CSS and JavaScript files in your blade layouts:

```html
<!-- CSS -->
<link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">

<!-- JavaScript -->
<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>
```

## Note
The actual asset files are not included in version control. Download and install them manually following the setup instructions above.
