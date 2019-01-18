package fr.quenti.tools;

import java.io.BufferedInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.URL;

/**
 * Classe basique pour la gestion de fichier internet
 * @author quenti77
 * @version 0.0.0.1-dev
 */
public class Transfer {

	/**
	 * Permet de télécharger des fichiers
	 * @param url
	 * @param destination
	 * @throws IOException 
	 */
	public static void downloadFile(String url, File destination) throws IOException {
		
		URL link = new URL(url);
		
		InputStream in = new BufferedInputStream(link.openStream());
		ByteArrayOutputStream out = new ByteArrayOutputStream();
		
		byte[] buf = new byte[1024];
		int n = 0;
		
		while ((n = in.read(buf)) != -1) {
			out.write(buf, 0, n);
		}
		
		out.close();
		in.close();
		byte[] response = out.toByteArray();
		
		FileOutputStream fos = new FileOutputStream(destination);
		fos.write(response);
		fos.close();
	}
	
}
