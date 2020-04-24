<?php

namespace BeyondCode\HeloLaravel;

use Illuminate\Contracts\Mail\Mailer as MailerContract;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailer as LaravelMailer;
use Illuminate\Support\Facades\View;
use ReflectionClass;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

class Mailer extends LaravelMailer implements MailerContract
{
    public function send($view, array $data = [], $callback = null)
    {
        if ($view instanceof Mailable
            && ! $view instanceof ShouldQueue
        ) {
            $this->applyDebugHeaders($view);
        }

        parent::send($view, $data, $callback);
    }

    protected function applyDebugHeaders(Mailable $mailable)
    {
        $mailable->withSwiftMessage(function (\Swift_Message $swiftMessage) use ($mailable) {
            $viewFile = $this->getMailableViewFile($mailable);
            $view = $this->getMailableView($viewFile);
            $viewContent = $this->getMailableViewContent($view);

            $viewData = $this->getMailableViewData($mailable);

            /**
             * We need to base64 encode the data, as the SMTP header mime encoding could add unwanted
             * CLRF line breaks.
             */
            $headers = $swiftMessage->getHeaders();
            $headers->addTextHeader('X-HELO-View', base64_encode($viewContent));
            $headers->addTextHeader('X-HELO-View-File', base64_encode($viewFile));
            $headers->addTextHeader('X-HELO-View-Data', base64_encode($viewData));
        });
    }

    protected function getMailableProperty($mailable, string $property)
    {
        $reflection = new ReflectionClass($mailable);
        $property = $reflection->getProperty($property);

        $property->setAccessible(true);

        return $property->getValue($mailable);
    }

    protected function getMailableViewFile(Mailable $mailable)
    {
        if (!is_null($markdown = $this->getMailableProperty($mailable, 'markdown'))) {
            return $markdown;
        }

        return $this->getMailableProperty($mailable, 'view');
    }

    protected function getMailableView(string $viewFile)
    {
        return View::make($viewFile);
    }

    protected function getMailableViewContent($view)
    {
        return file_get_contents($view->getPath());
    }

    protected function getMailableViewData(Mailable $mailable)
    {
        $dumper = new HtmlDumper();
        $cloner = new VarCloner();
        $clonedData = $cloner->cloneVar($mailable->buildViewData());

        return $dumper->dump($clonedData, true, [
            'maxDepth' => 3,
            'maxStringLength' => 160,
        ]);
    }
}
