<?
//���ļ�������չ�ɼ����
/*-----------------------------------------
�ɼ���չ����ı�д�淶

��һ��Ĳɼ������У�ͨ��ֻ��ʵ��ץȡҳ��ĳ�������ݵĹ��ܣ��޷���ͨ�õķ�ʽ������Щ���ݣ����磺�Ҳɼ�����һЩ�������ӣ����ɼ������ǹ̶��ģ���������ͨ�ô�����Щ���⣬ͨ����չ����Ϳ���ͨ������Щ�ɼ�����ԭʼ�������´�����Ȼ�󷵻�����������ݡ�
��չ����һ�㶼�� $sourceValue ����������ɼ���ԭʼ���ݣ�Ȼ�󷵻ؾ���������Ľ����
��չ�����ڲɼ������ļ��У��������·�ʽ��ʾ�ģ�
�磺
<dede:note field='title' value='[var:����]' function='TestMyPlus("@me")'>
   <dede:match><title>[var:����]</title></dede:match>
</dede:note>
����һ���ɼ���ҳ��������ӣ�ͨ�� function='TestMyPlus("@me")' ����ᷢ���ڲɼ�����ж���TestMyPlus�������صĶ�����
-----------------------------------------*/
function TestMyPlus($sourceValue)
{
	return "�����Ҽ��˵㶫���������� ".$sourceValue;
}

?>