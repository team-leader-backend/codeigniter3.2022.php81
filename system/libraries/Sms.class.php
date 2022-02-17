<?php

namespace Sms\Xml;
/**
 * @version 1.1
 */
abstract class Request
{
    public $login;
    public $password;
    public $url;
    public $error;
    public $response;
    public $item = array();

    /**
     * Создание подключения.
     *
     * @param string $login    логин в системе
     * @param string $password пароль в системе
     */
    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    /**
     * Получить подробный ответ
     *
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Получить сообщение ошибки.
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Установить значение url.
     *
     * @param string $url url например https://my5.t-sms.ru/
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Отправка xml на сервер
     * @return array
     */
    public function send()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/xml; charset=utf-8'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CRLF, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->generateXml());
        curl_setopt($ch, CURLOPT_URL, $this->getUrl());
        $result = curl_exec($ch);
        curl_close($ch);
        $this->response = static::parseXml($result);

        return static::parseXml($result);
    }
}

/**
 * Фабрика.
 */
interface Factory
{
    public function send();
    public function setUrl($url);
    public function getError();
}

/**
 * Продукт
 */
interface Product
{
    /**
     * Возвращает название продукта.
     *
     * @return string
     */
    public function getName();
}

/**
 * Создание сообщения.
 */
class Messages extends Request implements Factory
{
    /**
     * Создаем новое сообщение.
     *
     * @param string $sender отправитель SMS. Именно это значение будет выводиться на телефоне абонента в поле от кого SMS
     * @param string $text   текст обычного SMS или описание WAP ссылки
     * @param string $type   тип отправляемого SMS сообщения
     *
     * @return Message
     */
    public function createNewMessage($sender, $text, $type = 'sms')
    {
        return new Message($sender, $text, $type);
    }

    /**
     * Функция для добавления сообщения.
     *
     * @param object $obj объект сообщение
     */
    public function addMessage($obj)
    {
        $this->item[] = (object) $obj;
    }

    /**
     * Функция для формирования url.
     *
     * @return string
     */
    protected function getUrl()
    {
        return (string) "{$this->url}/xml/";
    }

    protected static function parseXml($xml)
    {
        $domXml = simplexml_load_string($xml);
        $arr = array();
        if (isset($domXml->error)) {
            $this->error = (string) $domXml->error;

            return;
        } else {
            $i = 0;
            foreach ($domXml->information as $val) {
                $arr[$i]['value'] = (string) $val;
                foreach ($val->attributes() as $attrName => $attrValue) {
                    $arr[$i][$attrName] = (string) $attrValue;
                }
                ++$i;
            }

            return $arr;
        }
    }

    protected function generateXml()
    {
        $domtree = new \DOMDocument('1.0', 'utf-8');
        $request = $domtree->appendChild($domtree->createElement('request'));

        //создание дерева security
        $security = $request->appendChild($domtree->createElement('security'));

        $domLogin = $domtree->createElement('login');
        $newDomLogin = $security->appendChild($domLogin);
        $newDomLogin->setAttribute('value', $this->login);

        $domPassword = $domtree->createElement('password');
        $newDomPassword = $security->appendChild($domPassword);
        $newDomPassword->setAttribute('value', $this->password);

        //создание дерева с сообщениями
        foreach ($this->item as $item) {
            $domMessage = $request->appendChild($domtree->createElement('message'));
            $domMessage->appendChild($domtree->createElement('sender', $item->getSender()));
            $domMessage->appendChild($domtree->createElement('text', $item->getText()));

            foreach ($item->abonents as $abonent) {
                $domAbonent = $domMessage->appendChild($domtree->createElement('abonent'));
                $domAbonent->setAttribute('phone', $abonent->getPhone());

                if ($abonent->getNumberSms()) {
                    $domAbonent->setAttribute('number_sms', $abonent->getNumberSms());
                }

                if ($abonent->getClientIdSms()) {
                    $domAbonent->setAttribute('client_id_sms', $abonent->getClientIdSms());
                }

                if ($abonent->getTimeSend()) {
                    $domAbonent->setAttribute('time_send', $abonent->getTimeSend());
                }

                if ($abonent->getValidityPeriod()) {
                    $domAbonent->setAttribute('validity_period', $abonent->getValidityPeriod());
                }
            }

            //добавление атрибута к message
            $domMessage->setAttribute('type', $item->getType());
        }

        return $domtree->saveXML();
    }
}

/**
 * Получение статуса сообщения/сообщений.
 */
class State extends Request implements Factory
{
    /**
     * Функция для добавления id сообщения.
     *
     * @param int $id_sms id сообщения
     */
    public function addIdSms($id_sms)
    {
        $this->item[] = (int) $id_sms;
    }

    /**
     * Функция для формирования url.
     *
     * @return string
     */
    protected function getUrl()
    {
        return (string) "{$this->url}/xml/state.php";
    }

    protected static function parseXml($xml)
    {
        $domXml = simplexml_load_string($xml);
        $arr = array();
        if (isset($domXml->error)) {
            $this->error = (string) $domXml->error;

            return;
        } else {
            $i = 0;
            foreach ($domXml->state as $val) {
                $arr[$i]['value'] = (string) $val;
                foreach ($val->attributes() as $attrName => $attrValue) {
                    $arr[$i][$attrName] = (string) $attrValue;
                }
                ++$i;
            }

            return $arr;
        }
    }

    protected function generateXml()
    {
        $domtree = new \DOMDocument('1.0', 'utf-8');
        $request = $domtree->appendChild($domtree->createElement('request'));

        //создание дерева security
        $security = $request->appendChild($domtree->createElement('security'));

        $domLogin = $domtree->createElement('login');
        $newDomLogin = $security->appendChild($domLogin);
        $newDomLogin->setAttribute('value', $this->login);

        $domPassword = $domtree->createElement('password');
        $newDomPassword = $security->appendChild($domPassword);
        $newDomPassword->setAttribute('value', $this->password);

        //создание дерева с сообщениями
        $domState = $request->appendChild($domtree->createElement('get_state'));

        foreach ($this->item as $id) {
            $domState->appendChild($domtree->createElement('id_sms', $id));
        }

        return $domtree->saveXML();
    }
}
/**
 * Получение баланса.
 */
class Balance extends Request implements Factory
{
    /**
     * Функция для формирования url.
     *
     * @return string
     */
    protected function getUrl()
    {
        return (string) "{$this->url}/xml/balance.php";
    }

    protected static function parseXml($xml)
    {
        $domXml = simplexml_load_string($xml);
        $arr = array();
        if (isset($domXml->error)) {
            $this->error = (string) $domXml->error;

            return;
        } else {
            $i = 0;
            foreach ($domXml->sms as $val) {
                $arr['sms'][$i]['value'] = (string) $val;
                foreach ($val->attributes() as $attrName => $attrValue) {
                    $arr['sms'][$i][$attrName] = (string) $attrValue;
                }
                ++$i;
            }
            $j = 0;
            foreach ($domXml->money as $val) {
                $arr['money'][$j]['value'] = (string) $val;
                foreach ($val->attributes() as $attrName => $attrValue) {
                    $arr['money'][$j][$attrName] = (string) $attrValue;
                }
                ++$j;
            }

            return $arr;
        }
    }

    protected function generateXml()
    {
        $domtree = new \DOMDocument('1.0', 'utf-8');
        $request = $domtree->appendChild($domtree->createElement('request'));

        //создание дерева security
        $security = $request->appendChild($domtree->createElement('security'));

        $domLogin = $domtree->createElement('login');
        $newDomLogin = $security->appendChild($domLogin);
        $newDomLogin->setAttribute('value', $this->login);

        $domPassword = $domtree->createElement('password');
        $newDomPassword = $security->appendChild($domPassword);
        $newDomPassword->setAttribute('value', $this->password);

        return $domtree->saveXML();
    }
}

/**
 * Получение входящих SMS
 */
class Incoming extends Request implements Factory
{
    /**
     * Функция для формирования url.
     *
     * @return string
     */
    protected function getUrl()
    {
        return (string) "{$this->url}/xml/incoming.php";
    }

    /**
     * Функция для установки периода с которого запрашиваются входящие SMS.
     *
     * @param string $start время (не включительно), с которого запрашиваются входящие SMS в формате YYYY-MM-DD hh:mm:ss
     * @param string $end   время (не включительно), по которое запрашиваются входящие SMS в формате YYYY-MM-DD hh:mm:ss
     */
    public function setTime($start, $end = null)
    {
        $this->item['start'] = $start;
        $this->item['end'] = $end;
    }

    protected static function parseXml($xml)
    {
        $domXml = simplexml_load_string($xml);
        $arr = array();
        if (isset($domXml->error)) {
            $this->error = (string) $domXml->error;

            return;
        } else {
            $i = 0;
            foreach ($domXml->sms as $val) {
                $arr['sms'][$i]['value'] = (string) $val;
                foreach ($val->attributes() as $attrName => $attrValue) {
                    $arr['sms'][$i][$attrName] = (string) $attrValue;
                }
                ++$i;
            }
            $j = 0;
            foreach ($domXml->money as $val) {
                $arr['money'][$j]['value'] = (string) $val;
                foreach ($val->attributes() as $attrName => $attrValue) {
                    $arr['money'][$j][$attrName] = (string) $attrValue;
                }
                ++$j;
            }

            return $arr;
        }
    }

    protected function generateXml()
    {
        $domtree = new \DOMDocument('1.0', 'utf-8');
        $request = $domtree->appendChild($domtree->createElement('request'));

        //создание дерева security
        $security = $request->appendChild($domtree->createElement('security'));

        $domLogin = $domtree->createElement('login');
        $newDomLogin = $security->appendChild($domLogin);
        $newDomLogin->setAttribute('value', $this->login);

        $domPassword = $domtree->createElement('password');
        $newDomPassword = $security->appendChild($domPassword);
        $newDomPassword->setAttribute('value', $this->password);

        //создание дерева временем
        $domTime = $request->appendChild($domtree->createElement('time'));
        $domTime->setAttribute('start', $this->item['start']);
        if (!empty($this->item['end'])) {
            $domTime->setAttribute('end', $this->item['end']);
        }

        return $domtree->saveXML();
    }
}

interface factoryMessages
{
    /**
     * Добавить номер абонента.
     *
     * @param string $phone номер телефона в международном формате (79201112233)
     */
    public function createAbonent($phone);
}

/**
 * Сообщение.
 */
class Message implements factoryMessages
{
    private $type;
    private $sender;
    private $text;
    public $abonents = array();

    /**
     * @param string $sender
     * @param string $text
     * @param string $type
     */
    public function __construct($sender, $text, $type = 'sms')
    {
        $this->type = $type;
        $this->sender = $sender;
        $this->text = $text;
    }

    /**
     * Добавить номер абонента.
     * @param string $phone номер телефона в международном формате (79201112233)
     * @return Abonent
     */
    public function createAbonent($phone)
    {
        return (object) new Abonent($phone);
    }

    /**
     * Добавить абонента.
     *
     * @param object $obj сформированный объект 
     */
    public function addAbonent($obj)
    {
        $this->abonents[] = (object) $obj;
    }

    /**
     * Получить тип сообщения.
     *
     * @return string тип сообщения
     */
    public function getType()
    {
        return htmlspecialchars($this->type, ENT_XML1);
    }

    /**
     * Получить отправителя.
     *
     * @return string отправитель
     */
    public function getSender()
    {
        return htmlspecialchars($this->sender, ENT_XML1);
    }

    /**
     * Получить Текст сообщения.
     *
     * @return string текст сообщения
     */
    public function getText()
    {
        return htmlspecialchars($this->text, ENT_XML1);
    }
}

class Abonent
{
    private $phone;
    private $number_sms;
    private $client_id_sms;
    private $time_send;
    private $validity_period;

    /**
     * Создание адресата.
     *
     * @param string $phone телефон в международно формате (например 79201112233)
     */
    public function __construct($phone)
    {
        $this->phone = $phone;
    }

    /**
     * Установить номер сообщения в пределах отправляемого XML документа.
     *
     * @param int $number_sms номер сообщения в пределах отправляемого XML документа
     */
    public function setNumberSms($number_sms)
    {
        $this->number_sms = $number_sms;
    }

    /**
     * Функция получения номера сообщения.
     *
     * @return int номер сообщения
     */
    public function getNumberSms()
    {
        return (int) $this->number_sms;
    }

    /**
     * Установить уникальный id смс
     *
     * @param int $client_id_sms если SMS с таким номером было отправлено, то повторная отправка не производится, а возвращается номер ранее  отправленного SMS
     */
    public function setClientIdSms($client_id_sms)
    {
        $this->client_id_sms = (int) $client_id_sms;
    }

    /**
     * Функция получения id sms.
     *
     * @return int
     */
    public function getClientIdSms()
    {
        return (int) $this->client_id_sms;
    }

    /**
     * Установить дату и время отправки в формате: YYYY-MM-DD hh:mm.
     *
     * @param type $time_send lата и время отправки в формате: YYYY-MM-DD hh:mm
     */
    public function setTimeSend($time_send)
    {
        $this->time_send = $time_send;
    }

    /**
     * Получить дату и время отправки в формате: YYYY-MM-DD hh:mm.
     *
     * @return string
     */
    public function getTimeSend()
    {
        return $this->time_send;
    }

    /**
     * дата и время, после которых не будут делаться попытки доставить SMS в формате:  YYYY-MM-DD hh:mm.
     *
     * @param string $validity_period
     */
    public function setValidityPeriod($validity_period)
    {
        $this->validity_period = $validity_period;
    }

    /**
     * Получение дата и времени после которых не будут делаться попытки доставить.
     *
     * @return string
     */
    public function getValidityPeriod()
    {
        return $this->validity_period;
    }

    /**
     * Вернуть номер абонента.
     *
     * @return string номер абонента 
     */
    public function getPhone()
    {
        return $this->phone;
    }
}