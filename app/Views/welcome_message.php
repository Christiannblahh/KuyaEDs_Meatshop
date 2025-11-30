<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to CodeIgniter 4!</title>
    <meta name="description" content="The small framework with powerful features">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">

    <!-- STYLES -->
    <style>
        * {
            transition: background-color 300ms ease, color 300ms ease;
        }
        :root {
            --dark: #0d1117;
            --darker: #161b22;
            --darkest: #010409;
            --light: #f0f6fc;
            --lighter: #f0f6fc;
            --primary: #2f81f7;
            --text: #7d8590;
            --text-light: #afb8c1;
        }
        @media (prefers-color-scheme: light) {
            :root {
                --dark: #ffffff;
                --darker: #f6f8fa;
                --darkest: #ffffff;
                --light: #0d1117;
                --lighter: #161b22;
                --primary: #0969da;
                --text: #656d76;
                --text-light: #424a53;
            }
        }
        body {
            font-family: -apple-system,BlinkMacSystemFont,"Segoe UI","Noto Sans",Helvetica,Arial,sans-serif;
            line-height: 1.5;
            color: var(--light);
            background: var(--dark);
            color-scheme: dark light;
        }
        .header {
            background-color: var(--darker);
            padding: 1rem;
        }
        .container {
            max-width: 920px;
            margin: 0 auto;
            padding: 4rem 2rem;
        }
        h1 {
            font-size: 2.5rem;
            font-weight: 600;
        }
        h2 {
            border-bottom: 1px solid var(--darker);
            margin-bottom: 1rem;
            margin-top: 2rem;
            padding-bottom: .3rem;
        }
        .flex {
            display: flex;
        }
        .f-wrap {
            flex-wrap: wrap;
        }
        .gap-2 {
            gap: 1rem;
        }
        .card {
            background-color: var(--darker);
            border: 1px solid var(--darkest);
            border-radius: 6px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .card h3 {
            margin-top: 0;
            color: var(--primary);
            font-size: 1.25rem;
        }
        .card p {
            margin-bottom: 0;
        }
        .text-center {
            text-align: center;
        }
        .text-small {
            font-size: 0.875rem;
            color: var(--text);
        }
        a {
            color: var(--primary);
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .footer {
            margin-top: 3rem;
            border-top: 1px solid var(--darker);
            padding-top: 2rem;
            text-align: center;
            color: var(--text);
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <strong>CodeIgniter 4</strong> - Welcome
        </div>
    </div>
    <div class="container">
        <h1 class="text-center">Welcome to CodeIgniter 4!</h1>

        <div class="flex f-wrap gap-2">
            <div class="card" style="flex: 1 1 20rem;">
                <h3>⚡ Quick Start Guide</h3>
                <p>
                    The User Guide contains an introduction, tutorial, a number of "how to"
                    guides, and then reference documentation for the components that make up
                    the framework.
                </p>
                <p>
                    <a href="https://codeigniter.com/user_guide" target="_blank">Read the User Guide</a>
                </p>
            </div>

            <div class="card" style="flex: 1 1 20rem;">
                <h3>📖 Learn More</h3>
                <p>
                    CodeIgniter 4 is a PHP framework that uses the Model-View-Controller (MVC)
                    architectural pattern. It provides a rich set of libraries for commonly
                    needed tasks, as well as a simple interface and logical structure to
                    access these libraries.
                </p>
                <p>
                    <a href="https://codeigniter.com/user_guide/intro/index.html" target="_blank">Learn More</a>
                </p>
            </div>
        </div>

        <div class="card">
            <h3>🎯 The Application</h3>
            <p>
                Your <strong>app</strong> directory contains the core code of your application. It is where
                you will build the majority of your application. The <strong>public</strong> directory is
                the entry point for your application and contains the <code>index.php</code> file.
            </p>
        </div>

        <div class="card">
            <h3>🔧 Configuration</h3>
            <p>
                The <strong>app/Config</strong> directory contains configuration files that you can use
                to configure your application. The <code>app/Config/App.php</code> file contains the
                base configuration for your application.
            </p>
        </div>

        <div class="card">
            <h3>📁 Important Directories</h3>
            <ul>
                <li><code>app/Controllers</code> - Your controllers</li>
                <li><code>app/Models</code> - Your models</li>
                <li><code>app/Views</code> - Your views</li>
                <li><code>app/Config</code> - Configuration files</li>
                <li><code>public</code> - Public assets and entry point</li>
            </ul>
        </div>

        <div class="footer text-small">
            <p>
                Page rendered in {elapsed_time} seconds.
                <?php if (ENVIRONMENT !== 'production') : ?>
                    CodeIgniter Version <strong><?= CodeIgniter\CodeIgniter::CI_VERSION ?></strong>
                <?php endif; ?>
            </p>
        </div>
    </div>
</body>
</html>

