import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.util.ArrayList;

import opennlp.tools.sentdetect.SentenceDetectorME;
import opennlp.tools.sentdetect.SentenceModel;


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
	
	
	public String genSummary()
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
	
	public String[] getSentences(String par)
	{
		InputStream modelIn = null;
		try
		{
			modelIn = new FileInputStream("en-sent.bin");
		}
		catch (FileNotFoundException e1)
		{
		}

		SentenceModel model = null;
		try {
		  model = new SentenceModel(modelIn);
		}
		catch (IOException e) {
		  e.printStackTrace();
		}
		finally {
		  if (modelIn != null) {
		    try {
		      modelIn.close();
		    }
		    catch (IOException e) {
		    }
		  }
		}
		
		SentenceDetectorME sentenceDetector = new SentenceDetectorME(model);
		return sentenceDetector.sentDetect(par);
		
	}
}
