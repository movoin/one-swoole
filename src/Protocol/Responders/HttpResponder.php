<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol\Responders
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol\Responders;

use One\Protocol\Factory;
use One\Context\Contracts\Payload;
use One\Protocol\Contracts\Request;
use One\Protocol\Contracts\Response;
use One\Protocol\Contracts\Responder;
use One\Protocol\Contracts\RespondHandler;
use One\Protocol\Responders\Handlers\JsonHandler;
use One\Protocol\Responders\Handlers\XmlHandler;
use Negotiation\Negotiator;
use Negotiation\EncodingNegotiator;

class HttpResponder implements Responder
{
    /**
     * 响应处理句柄
     *
     * @var array
     */
    protected $handlers = [];
    /**
     * 优先响应处理句柄
     *
     * @var array
     */
    protected $priorities = [];

    /**
     * Negotiator
     *
     * @var \Negotiation\Negotiator
     */
    private $negotiator;

    /**
     * 构造
     */
    public function __construct()
    {
        $this->handlers = [
            new JsonHandler,
            new XmlHandler
        ];

        $this->negotiator = new Negotiator;
    }

    /**
     * 响应请求
     *
     * @param  \One\Protocol\Contracts\Request  $request
     * @param  \One\Protocol\Contracts\Response $response
     * @param  \One\Context\Contracts\Payload   $payload
     *
     * @return \One\Protocol\Contracts\Response
     */
    public function __invoke(Request $request, Response $response, Payload $payload): Response
    {
        return $this->handlePayload(
            $request,
            $this->handleRequestHeader($request, $response, $payload),
            $payload
        );
    }

    /**
     * 处理截荷
     *
     * @param  \One\Protocol\Contracts\Request  $request
     * @param  \One\Protocol\Contracts\Response $response
     * @param  \One\Context\Contracts\Payload   $payload
     *
     * @return \One\Protocol\Contracts\Response
     */
    protected function handlePayload(Request $request, Response $response, Payload $payload): Response
    {
        $handler = $this->getHandler($request);

        return $response
            ->withBody(Factory::newStream($handler->body($payload)))
            ->withHeader('Content-Type', $handler->type())
            ->withStatus($payload->getCode())
        ;
    }

    /**
     * 处理请求头
     *
     * @param  \One\Protocol\Contracts\Request  $request
     * @param  \One\Protocol\Contracts\Response $response
     *
     * @return \One\Protocol\Contracts\Response
     */
    protected function handleRequestHeader(Request $request, Response $response): Response
    {
        // 处理 No-Cache
        if ($request->header('Cache-Control') === 'no-cache') {
            $response = $response->withHeader('Cache-Control', [
                'no-store',
                'no-cache',
                'must-revalidate'
            ]);
        }

        // 处理 Gzip 请求
        // 不知道为什么，老是出错，还需要再查查
        // https://blog.csdn.net/cs958903980/article/details/76890034?locationNum=6&fps=1
        //
        // $encoding = $request->getHeaderLine('Accept-Encoding');

        // if (! empty($encoding)) {
        //     $negotiator = new EncodingNegotiator;
        //     $preferred = $negotiator->getBest($encoding, ['gzip']);

        //     if (! empty($preferred)) {
        //         $response = $response->withHeader('Content-Encoding', 'gzip');
        //         $response->setGzip(1);
        //     }
        //     unset($negotiator, $preferred);
        // }
        // unset($encoding);

        return $response;
    }

    /**
     * 返回请求内容句柄
     *
     * @param  \One\Protocol\Contracts\Request $request
     *
     * @return \One\Protocol\Contracts\RespondHandler|null
     */
    public function getAcceptHandler(Request $request)
    {
        $accept     = $request->getHeaderLine('Accept');
        $priorities = $this->priorities();

        if (! empty($accept)) {
            $preferred = $this->negotiator->getBest($accept, array_keys($priorities));
        }

        if (! empty($preferred)) {
            $handler = $priorities[$preferred->getValue()];
        } else {
            return null;
        }

        unset($preferred, $accept, $priorities);

        return $handler;
    }

    /**
     * 获得响应处理句柄
     *
     * @param  \One\Protocol\Contracts\Request  $request
     *
     * @return \One\Protocol\Contracts\RespondHandler
     */
    protected function getHandler(Request $request): RespondHandler
    {
        if (($handler = $this->getAcceptHandler($request)) === null) {
            $priorities = $this->priorities();

            return array_shift($priorities);
        }

        return $handler;
    }

    /**
     * 返回响应器的优先级
     *
     * @return array
     */
    protected function priorities(): array
    {
        if ($this->priorities !== []) {
            return $this->priorities;
        }

        foreach ($this->handlers as $handler) {
            foreach ($handler->accepts() as $type) {
                $this->priorities[$type] = $handler;
            }
        }

        return $this->priorities;
    }
}
