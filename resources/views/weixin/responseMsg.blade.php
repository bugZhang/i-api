<xml>
    <ToUserName><![CDATA[{{ $FromUserName }}]]></ToUserName>
    <FromUserName><![CDATA[{{ $ToUserName }}]]></FromUserName>
    <CreateTime>{{ time() }}</CreateTime>
    <MsgType><![CDATA[{{$MsgType}}]]></MsgType>
    <Content><![CDATA[{{ $Content }}]]></Content>
</xml>