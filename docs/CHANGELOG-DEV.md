# Changelog Development

This is the development changelog, which includes changes that are not yet released. For released changes, check the [CHANGELOG.md](CHANGELOG.md) file.

## Step 3

// Continue... Most recent changes at the top.

## Step 2

Added Hero component:

```tsx
import { Hero, HeroContent, HeroTitle, HeroTagline, HeroActions, HeroImage } from "@/components/ui/hero";
import { Button } from "@/components/ui/button";

<Hero>
  <HeroContent>
    <HeroTitle>Make your docs with Plume</HeroTitle>
    <HeroTagline>This is Plume, a Markdown tool for content: pages, docs, wikis and more.</HeroTagline>
    <HeroActions>
      <Button>Get started</Button>
      <Button variant="ghost">View on GitHub</Button>
    </HeroActions>
  </HeroContent>
  <HeroImage src="/hero-light.png" dark="/hero-dark.png" alt="Hero" />
</Hero>
```

## Step 1 
We have a landing page which belongs to the playground.
It allows to navigate to 2 pages, which are defined routes in the playground, but the rendering is already being made in Plume.
It allows to render 2 different pages, and we provide the Props for that pages.
We're not sure if we're integrating 100% of Inertia, but in this step, yes.
Just added ShadCN and made the start of page1 and page2.
The next step is to improve pages that we can render, with some nice couple (2 or 3) sections.
