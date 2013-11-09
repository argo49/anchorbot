import java.util.ArrayList;


public class ArticleJSON
{
	private String summary;
	private ArrayList<EntityWrap> entwrap;
	
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
	

}
