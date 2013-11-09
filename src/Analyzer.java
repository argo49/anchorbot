import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStream;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import com.google.gson.Gson;

import edu.smu.tspell.wordnet.NounSynset;
import edu.smu.tspell.wordnet.Synset;
import edu.smu.tspell.wordnet.SynsetType;
import edu.smu.tspell.wordnet.WordNetDatabase;

import opennlp.tools.postag.POSModel;
import opennlp.tools.postag.POSTaggerME;
import opennlp.tools.sentdetect.SentenceDetectorME;
import opennlp.tools.sentdetect.SentenceModel;
import opennlp.tools.tokenize.Tokenizer;
import opennlp.tools.util.InvalidFormatException;


public class Analyzer
{

	/**
	 * @param args
	 * @throws IOException 
	 * @throws InvalidFormatException 
	 */
	public static void main(String[] args) throws InvalidFormatException, IOException
	{
		System.setProperty("wordnet.database.dir", "WordNet-3.0\\dict\\");
		Gson gson = new Gson();
		//Improve!
		ReturnValue retVal = new ReturnValue();
		ArrayList<ArticleJSON> arts = new ArrayList<ArticleJSON>();
		for(int i = 0; i < args[i].length(); i++)
		{
			//Text file with scrapping
			if(args[i].contains("alchemy"))
			{
				AlchemyJSONParser alcParser = new AlchemyJSONParser("temp/" + args[i]);
				ArrayList<Entity> ents = alcParser.getEntities();
				ArticleJSON art = new ArticleJSON();
				
				for(int j = 0; j < args[j].length(); j++)
				{
					txtJSONParser txtParser = new txtJSONParser("temp/" + args[i]);
					ArrayList<String> pars = txtParser.getParagraphs();
					String url = txtParser.getURL();
					
					if(!args[j].contains("alchemy") && alcParser.getUrl().equals(url))
					{
						//Found a match
						Summarizer sum = new Summarizer();
						sum.setPar(pars);
						art.setSummary(sum.genSummary());
					}
				}
				
				art.setEntwrap(buildEntsJSON(ents, alcParser.getText())); 
				arts.add(art);
			}
			retVal.setArticles(arts);
		}
		
		FileWriter fw = new FileWriter(new File("temp/return-"));
		fw.write(gson.toJson(retVal));
			
			
	}
	
	public static HashMap<String, ArrayList<String>> getFlavour(String text, ArrayList<Entity> ents) throws InvalidFormatException, IOException
	{
		TextAnalyser ta = new TextAnalyser(text, ents);
		String []words = ta.getWords();
		Tokenizer _tokenizer = null;
		 
		InputStream modelIn = null;
		InputStream is = new FileInputStream("/en-pos-maxent.bin");
		final POSModel posModel = new POSModel(is);
		modelIn.close();
		 
		POSTaggerME _posTagger = new POSTaggerME(posModel);
		 
		String[] pos = _posTagger.tag(words);
		HashMap<String, ArrayList<String>> adjs = new HashMap<String, ArrayList<String>>();
		for(int i = 0; i<pos.length; i++)
		{
			if(pos[i].contains("JJ"))
			{
				for(int m = 0; m < ents.size(); m++)
				{
					
						ArrayList<String> a = new ArrayList<String>();
						if( Math.abs(i-m) < 2)
						{
							a.add(words[i]);
						}
							
						adjs.put(ents.get(m).getText(), a);
						
					}
				}
				
				
			}
		 
		WordNetDatabase database = WordNetDatabase.getFileInstance();
		NounSynset nounSynset; 
		HashMap<String, ArrayList<String>> retVal = new HashMap<String, ArrayList<String>>();
		
		for(int j = 0; j < adjs.size() ; j++)
		{
			String ent = (String) adjs.keySet().toArray()[j];
			ArrayList<String> adjectives = (ArrayList<String>) adjs.values().toArray()[j];
			HashMap<String, Integer> descriptions = new HashMap<String, Integer>();
			
			for(int n = 0; n < adjectives.size(); n++ )
			{
				Synset[] synsets = database.getSynsets(adjectives.get(n), SynsetType.ADJECTIVE);
				for(int m = 0; m < synsets.length; m++)
				{
					if(descriptions.get(synsets[m].getDefinition()) == null)
					{
						descriptions.put(synsets[m].getDefinition(), 1);
					}else
						descriptions.put(synsets[m].getDefinition(), descriptions.get(synsets[m].getDefinition())+1);
				}
			}
			
			Map.Entry<String, Integer> maxEntry = null;

			for (Map.Entry<String, Integer> entry : descriptions.entrySet())
			{
			    if (maxEntry == null || entry.getValue().compareTo(maxEntry.getValue()) > 0)
			    {
			        maxEntry = entry;
			    }
			}
			
			//Max adjective + num for entity
			ArrayList<String> tmp = new ArrayList<String>();
			tmp.add(maxEntry.getKey());
			tmp.add(maxEntry.getValue().toString());
			retVal.put(ent, tmp);
			
		}
	
		return retVal;
		
	}



	public static ArrayList<EntityWrap> buildEntsJSON(ArrayList<Entity> ents, String text) throws InvalidFormatException, IOException
	{
		ArrayList<EntityWrap> ret = new ArrayList<EntityWrap>();
		Gson gson = new Gson();
		HashMap<String, ArrayList<String>> flavours = getFlavour(text, ents);
		
		for(int i = 0; i < ents.size(); i++)
		{
			EntityWrap temp = new EntityWrap();
			temp.setName(ents.get(i).getText());
			temp.setType(ents.get(i).getType());
			temp.setRelevance(ents.get(i).getRelevance());
			temp.setSentiment(flavours.get(ents.get(i).getText()).get(0));
			temp.setNumAdjective(flavours.get(ents.get(i).getText()).get(1));
			
			ret.add(temp);
		}
		
		return ret;
	}

}
