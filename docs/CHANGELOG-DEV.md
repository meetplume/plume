# Changelog Development

This is the development changelog, which includes changes that are not yet released. For released changes, check the [CHANGELOG.md](CHANGELOG.md) file.

## Step 3

// Continue... Most recent changes at the top.

## Step 2

Added Footer component:

```tsx
import { Footer, FooterLabel, FooterLogo, FooterDescription, FooterLinks } from "@/components/ui/footer";

<Footer>
  <FooterLabel>Brought to you by</FooterLabel>
  <FooterLogo>
    <img src="/logo.svg" alt="Plume" className="h-12" />
  </FooterLogo>
  <FooterDescription>
    Plume is a Markdown tool for content: pages, docs, wikis and more.
  </FooterDescription>
  <FooterLinks>
    <a href="/docs">Learn about Plume</a>
  </FooterLinks>
</Footer>
```

## Step 1 
We have a landing page which belongs to the playground.
It allows to navigate to 2 pages, which are defined routes in the playground, but the rendering is already being made in Plume.
It allows to render 2 different pages, and we provide the Props for that pages.
We're not sure if we're integrating 100% of Inertia, but in this step, yes.
Just added ShadCN and made the start of page1 and page2.
The next step is to improve pages that we can render, with some nice couple (2 or 3) sections.
