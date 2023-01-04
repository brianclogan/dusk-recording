# About Dusk Recordings

This packge is forked from [Sander van Kasteel's](https://github.com/sandervankasteel/) [Laravel Dusk Screenrecordings](https://github.com/sandervankasteel/laravel-dusk-screenrecordings).

This update brings it to the current versions of Laravel, and is being worked on to include Firefox and Edge support.

## Usage
### Warning: Currently only Chrome is supported 

> **Requires [PHP 8.0+](https://php.net/releases/)**
> 
> **Requires Laravel 8 (or higher)**

1. First install the pacakge
```
composer require --dev brianclogan/dusk-recordings
```

2. Publish the configuration

```
php artisan recordings:publish
```

3. Modify your `tests/DuskTestcase.php` to have screen recordings work.

### Example implmentation
```php
    protected function driver()
    {
        $options = (new ChromeOptions)
            ->setExperimentalOption("prefs", [
                "download.default_directory" => $this->downloadDir
            ])
        ->addArguments(collect([
            '--window-size=1920,1080',
        ])->unless($this->hasHeadlessDisabled(), function ($items) {
            return $items->merge([
                '--disable-gpu',
                '--headless',
            ]);
        })->merge(
            $this->getChromeArgs()
        )->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }
```

The most important parts are the 
```php
$this->getChromeArgs()
``` 

and the

```php
->setExperimentalOption("prefs", [
                "download.default_directory" => $this->downloadDir
            ])
```
Firstly, we need to get a specific set of arguments, so that Chrome will allow us to record from an non-HTTPS source and
automatically select the source from which to record from. 

Lastly, we need to set `download.default_directory` to a known directory, so we know from which directory we need to move our
recordings from.

4. Add the `WithScreenRecordings` to your `tests/DuskTestCase.php`, so we will actually record and store the recordings. 

```php
use Brianclogan\DuskRecordings\WithDuskRecordings;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication,
        WithDuskRecordings;
        
    // Rest of your DuskTestCase
}
```

5. Run `php artisan dusk` to see it in action 🥳 🚀

## How it works

This package works by loading a browser plugin, which in turns creates a new (pinned) tab in the browser, 
then it initializes the recording and then executes the test as usual. 

Once the tests are finished, based on the configuration it will 'download' the recording from the tab created by 
the browser extension.  

## TODO
  - Add Firefox support
  - Fix `--auto-select-desktop-capture-source` argument under Linux
  - Ideal case scenario: enable window recording (instead of the full desktop).
