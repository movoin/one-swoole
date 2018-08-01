<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol\Responders\Handlers
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol\Responders\Handlers;

use XMLWriter;
use One\Context\Contracts\Payload;
use One\Protocol\Contracts\RespondHandler;

class XmlHandler implements RespondHandler
{
    /**
     * 接受类型
     *
     * @return array
     */
    public function accepts(): array
    {
        return [
            'application/xml'
        ];
    }

    /**
     * 内容类型
     *
     * @return string
     */
    public function type(): string
    {
        return 'application/xml';
    }

    /**
     * 内容处理
     *
     * @param  \One\Context\Contracts\Payload $payload
     *
     * @return string
     */
    public function body(Payload $payload): string
    {
        return $this->getXML($payload->toArray());
    }

    /**
     * 获得 XML
     *
     * @param  array $body
     *
     * @return string
     */
    protected function getXML(array $body): string
    {
        $this->xmlWriter = new XMLWriter;

        $this->xmlWriter->openMemory();
        $this->xmlWriter->setIndent(true);
        $this->xmlWriter->setIndentString('');
        $this->xmlWriter->startDocument('1.0', 'UTF-8');
        $this->xmlWriter->startElement('data');

        $this->fromArray($body);

        $this->xmlWriter->endElement();
        $this->xmlWriter->endDocument();

        return str_replace("\n", '', $this->xmlWriter->outputMemory());
    }

    /**
     * 处理嵌套数据
     *
     * @param  array $array
     */
    protected function fromArray(array $array)
    {
        if (is_array($array)) {
            foreach ($array as $index => $element) {
                if (is_array($element)) {
                    $this->xmlWriter->startElement($index);
                    $this->fromArray($element);
                    $this->xmlWriter->endElement();
                } else {
                    $this->xmlWriter->startElement($index);
                    $this->xmlWriter->text($element);
                    $this->xmlWriter->endElement();
                }
            }
        }
    }
}
