<xml>
    <ToUserName><![CDATA[{{ $message->FromUserName }}]]></ToUserName>
    <FromUserName><![CDATA[{{ $message->ToUserName }}]]></FromUserName>
    <CreateTime>{{ time() }}</CreateTime>
    <MsgType><![CDATA[text]]></MsgType>
    <Content><![CDATA[{{ $message->Content }}]]></Content>
</xml>