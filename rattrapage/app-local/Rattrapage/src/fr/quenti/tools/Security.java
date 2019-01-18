package fr.quenti.tools;

import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;

/**
 * Méthode de cryptage
 * @author quenti77
 * @version 0.0.0.1-dev
 */
public class Security {

	/**
	 * Permet de hasher un string en MD5(string)
	 * @param password : Le mot de passe à hasher
	 * @return le mot de passe hashé
	 */
	public static String encode(String password)
	{
	    byte[] uniqueKey = password.getBytes();
	    byte[] hash      = null;
	
	    try
	    {
	        hash = MessageDigest.getInstance("MD5").digest(uniqueKey);
	    }
	    catch (NoSuchAlgorithmException e)
	    {
	        throw new Error("No MD5 support in this VM.");
	    }
	
	    StringBuilder hashString = new StringBuilder();
	    for (int i = 0; i < hash.length; i++)
	    {
	        String hex = Integer.toHexString(hash[i]);
	        if (hex.length() == 1)
	        {
	            hashString.append('0');
	            hashString.append(hex.charAt(hex.length() - 1));
	        }
	        else
	            hashString.append(hex.substring(hex.length() - 2));
	    }
	    return hashString.toString();
	}
	
}

