import Markdown from 'react-markdown';
import remarkGfm from 'remark-gfm';
import rehypeRaw from 'rehype-raw';
import remarkFrontmatter from 'remark-frontmatter';
import rehypeSanitize from 'rehype-sanitize';

export default function App({ appName, title, content }) {
    return (
        <div className="bg-background p-4">
            <div className="m-auto prose max-w-2xl space-y-6">
                <Markdown
                    remarkPlugins={[
                        remarkGfm,
                        remarkFrontmatter,
                    ]}
                    rehypePlugins={[
                        rehypeRaw,
                        rehypeSanitize,
                    ]}
                >
                    {content}
                </Markdown>
            </div>
        </div>
    );
}
