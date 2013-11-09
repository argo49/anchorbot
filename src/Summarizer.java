import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.util.ArrayList;

import opennlp.tools.sentdetect.SentenceDetectorME;
import opennlp.tools.sentdetect.SentenceModel;
import opennlp.tools.util.InvalidFormatException;


public class Summarizer
{
	private ArrayList<String> par;
	
	public Summarizer()
	{
		
	}
	
	public void setPar(ArrayList<String> par)
	{
		this.par = par;
	}
	
	public ArrayList<String> getPar()
	{
		return par;
	}
	
	
	public String genSummary() throws InvalidFormatException, IOException
	{
		String summary = "";
		for(int i = 0; i < par.size(); i++)
		{
			if(i == 0)
			{
			String[] sentences = this.getSentences(par.get(i));
			summary.concat(sentences[0]);
			if(sentences.length >= 1)
				summary.concat(sentences[1]);
			}
			
			if(i == par.size() - 1)
			{
				String[] sentences = this.getSentences(par.get(i));
				summary.concat(sentences[sentences.length-1]);
			}
		}
		return summary;
	}
	
	public String[] getSentences(String par) throws InvalidFormatException, IOException
	{
		 
		// always start with a model, a model is learned from training data
		InputStream is = new FileInputStream("en-sent.bin");
		SentenceModel model = new SentenceModel(is);
		SentenceDetectorME sdetector = new SentenceDetectorME(model);
 
		return sdetector.sentDetect(par);
 
		
	}
}
