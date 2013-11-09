import java.util.ArrayList;


public class TextAnalyser
{

	private String text;
	private String[] words;
	private ArrayList<Entity> ents;
	
	public TextAnalyser(String text, ArrayList<Entity> ents)
	{
		this.text = text;
		this.words = text.split("\\s+");
		this.ents = ents;
	}
	public void setText(String text)
	{
		this.text = text;
	}
	public String getText()
	{
		return text;
	}
	
	public String[] getWords()
	{
		return this.words;
	}
	

	

}
