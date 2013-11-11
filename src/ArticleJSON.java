import java.util.ArrayList;


public class ArticleJSON
{
	private String summary;
	private ArrayList<EntityWrap> entwrap;
	private String url;
	
	public ArticleJSON()
	{
		// TODO Auto-generated constructor stub
	}
	public void setEntwrap(ArrayList<EntityWrap> entwrap)
	{
		this.entwrap = entwrap;
	}
	public void setSummary(String summary)
	{
		this.summary = summary;
	}
	public void setUrl(String url)
	{
		this.url = url;
	}
	

}
