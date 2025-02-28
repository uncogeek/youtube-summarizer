# 🎬 YouTube Summarizer

![YouTube Summarizer](https://img.shields.io/badge/Version-1.0-brightgreen) ![PHP](https://img.shields.io/badge/PHP-8.0+-blueviolet) ![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-blue) ![License](https://img.shields.io/badge/License-MIT-orange)

> **Transform lengthy YouTube videos into concise, customizable summaries with just a click!** ✨

## ✨ Features That Will Make You Smile

This powerful YouTube Summarizer application brings incredible flexibility to your video content consumption:

- 🎯 **Dual Processing Modes** - Choose between full transcripts or AI-generated summaries
- 🌐 **Multi-Language Support** - Get summaries in English, Persian, Spanish, and many more languages!
- 😊 **Emoji Toggle** - Add fun and visual clarity with emoji-enhanced summaries
- 🤖 **AI Provider Flexibility** - Seamlessly switch between OpenAI and OpenRouter
- 🎭 **20+ Tone Options** - From Authoritative to Warm, customize how your summary feels
- 📝 **40+ Output Formats** - Transform content into Blogs, FAQs, Tutorials, and more
- 🖋️ **19 Writing Styles** - Academic, Narrative, Technical - you name it!
- 📥 **Export to DOCX** - Save your summaries as formatted Word documents
- 📋 **Rich Formatting** - Beautiful headings, bullet points, and markdown rendering
- 🔀 **RTL/LTR Support** - Perfect for both Latin and Arabic/Persian scripts

## 📸 Screenshots


<a href="/screenshot/youtube-summarizer-screenshot.png" target="_blank">
  <img src="/screenshot/youtube-summarizer-screenshot.png" width="300" />
</a>

*The sleek, user-friendly interface makes generating summaries a breeze!*

## 🚀 How It Works

1. Enter a YouTube URL in the input field
2. Choose your preferred processing options (summary/transcript, language, etc.)
3. Click "Go" and watch as the magic happens!
4. Copy, download, or directly read your beautifully formatted results

## 🛠️ Installation

Getting started is super simple:

```bash
# Clone the repository
git clone https://github.com/uncogeek/youtube-summarizer.git

# Navigate to the project directory
cd youtube-summarizer

# Serve with your preferred PHP server
php -S localhost:8000
```

Alternatively, you can just clone the repository, modify `config.php`, and start using the application! 🚀

Certainly! Here's a suggested section to add to your GitHub README:

---

## 🌐 Inline URL Usage

To quickly get a summary of a YouTube video, follow these steps:

1. Ensure that your application is running on a server as explained in the [Installation](#installation) section.
2. Use the following URL pattern to obtain a summary for any YouTube video:

```
https://your-hosted-repo.domain/?url=https://www.youtube.com/watch?v=VIDEO_ID
```

- Replace `your-hosted-repo.domain` with the actual domain where your app is hosted.
- Substitute `VIDEO_ID` with the unique identifier of the YouTube video you want to summarize, like in this example:

```
https://your-hosted-repo.domain/?url=https://www.youtube.com/watch?v=NikAa447TQE
```

Simply enter the above URL in your browser to view the video summary. Enjoy quick insights with ease! 📹✍️


## 🔑 API Keys

To use this application, you'll need:

1. An API key from a YouTube transcript service (youtube-transcript.io or supadata.ai)
2. An OpenAI API key or OpenRouter API key

Add your API keys to the respective PHP files before using. (Edit config.php to replace your API keys.)

## 📋 Requirements

- PHP 8.0+

## 📚 Technical Overview

This application is built with love using:

- **PHP** for backend processing
- **JavaScript/jQuery** for dynamic frontend functionality
- **Bootstrap 5** for the beautiful, responsive design
- **AJAX** for seamless, no-refresh operation

The architecture follows a clean pattern:
1. Frontend captures user preferences
2. AJAX request fetches video transcript
3. Content is processed through AI (or returned directly)
4. Results are beautifully formatted and displayed

## 💖 Why You'll Love It

This YouTube Summarizer isn't just another tool - it's a complete content transformation system! Whether you're:

- A student needing to quickly understand lecture videos
- A researcher gathering information efficiently
- A content creator looking for inspiration
- A busy professional who values time

...this application will revolutionize how you consume YouTube content!

## 🤝 Contributing

I'd be thrilled to have your contributions! Here's how:

1. Fork the repository
2. Create your feature branch (`git checkout -b amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin amazing-feature`)
5. Open a Pull Request

## 📜 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Thanks to OpenAI and OpenRouter for their amazing AI APIs
- YouTube transcript services for making content accessible
- Bootstrap team for the beautiful frontend framework
- All the coffee that fueled this development! ☕

---

Made with ❤️ by UncoGeek

*Transform the way you consume YouTube content - one summary at a time!*
