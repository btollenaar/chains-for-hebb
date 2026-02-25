#!/usr/bin/env python3
"""
Generate site imagery for Chains for Hebb using Google Gemini 2.5 Flash.

Usage:
    cd project-root
    scripts/.venv/bin/python scripts/generate-images.py

Reads GEMINI_API_KEY from the project .env file (gitignored).
Output: public/images/generated/*.webp
"""

import os
import sys
from pathlib import Path

try:
    from google import genai
    from PIL import Image
    import io
except ImportError:
    print("Install dependencies: pip install google-genai Pillow")
    sys.exit(1)

PROJECT_ROOT = Path(__file__).parent.parent


def load_dotenv_key(key: str) -> str | None:
    """Read a single key from the project .env file."""
    env_path = PROJECT_ROOT / ".env"
    if not env_path.exists():
        return None
    for line in env_path.read_text().splitlines():
        line = line.strip()
        if line.startswith(f"{key}="):
            return line.split("=", 1)[1].strip().strip('"').strip("'")
    return None


API_KEY = os.environ.get("GEMINI_API_KEY") or load_dotenv_key("GEMINI_API_KEY")
if not API_KEY:
    print("GEMINI_API_KEY not found in environment or .env file")
    sys.exit(1)

OUTPUT_DIR = Path(__file__).parent.parent / "public" / "images" / "generated"
OUTPUT_DIR.mkdir(parents=True, exist_ok=True)

client = genai.Client(api_key=API_KEY)

IMAGES = [
    {
        "filename": "hero-hebb-park.webp",
        "prompt": (
            "A stunning wide-angle photograph of a Pacific Northwest park at golden hour. "
            "Tall Douglas fir trees frame the scene with a glimpse of the Willamette River in the background. "
            "A disc golf basket is visible in the middle distance on a grassy fairway. "
            "Warm golden sunlight filters through the canopy. Cinematic quality, 16:9 aspect ratio. "
            "Lush green grass and ferns. No people, just serene nature."
        ),
    },
    {
        "filename": "mission-aerial.webp",
        "prompt": (
            "An aerial drone photograph looking down at a forested park with clearings and trails. "
            "Pacific Northwest landscape with evergreen trees, a meandering river nearby. "
            "Some clearings suggest disc golf fairways winding through the trees. "
            "Bright daylight, vivid green foliage. Slightly overhead angle. No text or labels."
        ),
    },
    {
        "filename": "donate-hero-basket.webp",
        "prompt": (
            "A close-up photograph of a disc golf basket (chain catcher target) in a forest setting. "
            "Golden hour light catches the metal chains beautifully. "
            "Dense Pacific Northwest forest (Douglas fir, western red cedar) in the soft-focus background. "
            "The basket is the Innova DISCatcher model, bright yellow top. "
            "Professional sports photography style, shallow depth of field."
        ),
    },
    {
        "filename": "events-hero-community.webp",
        "prompt": (
            "A group of 8-10 diverse people standing in a park clearing surrounded by tall evergreen trees. "
            "They look happy and casual, some holding disc golf discs. Community volunteer vibe. "
            "Pacific Northwest setting, overcast sky typical of Oregon. "
            "Some are wearing work gloves, suggesting a park work party. "
            "Natural lighting, documentary photography style."
        ),
    },
    {
        "filename": "progress-hero.webp",
        "prompt": (
            "A concept photo of a disc golf course under construction in a forest park. "
            "A concrete tee pad is being poured in the foreground, with a disc golf basket installed "
            "in the distance. Pacific Northwest forest setting with tall Douglas fir trees. "
            "Freshly cleared fairway with mulch and natural landscaping. "
            "Bright daylight, construction progress feel."
        ),
    },
    {
        "filename": "gallery-placeholder-1.webp",
        "prompt": (
            "A scenic view of the Willamette River from a forested bluff. "
            "Pacific Northwest landscape with evergreen trees, ferns, and mossy rocks. "
            "Clear blue sky with some clouds. Nature photography style."
        ),
    },
    {
        "filename": "gallery-placeholder-2.webp",
        "prompt": (
            "A forest trail winding through tall Douglas fir trees in Oregon. "
            "Dappled sunlight on the path. Ferns and moss on both sides. "
            "Peaceful and inviting. Nature photography."
        ),
    },
    {
        "filename": "gallery-placeholder-3.webp",
        "prompt": (
            "An open meadow clearing in a Pacific Northwest forest park, perfect for disc golf. "
            "Surrounded by tall evergreen trees. Bright green grass. "
            "A few wildflowers. Sunny day with blue sky."
        ),
    },
    {
        "filename": "gallery-placeholder-4.webp",
        "prompt": (
            "A great blue heron standing at the edge of a river in an Oregon park. "
            "Peaceful wildlife scene with reflections on the water. "
            "Forest in the background. Nature photography."
        ),
    },
]


def generate_image(prompt: str, filename: str) -> bool:
    """Generate a single image and save as WebP."""
    output_path = OUTPUT_DIR / filename
    if output_path.exists():
        print(f"  Skipping {filename} (already exists)")
        return True

    print(f"  Generating {filename}...")
    try:
        response = client.models.generate_content(
            model="gemini-2.0-flash-exp-image-generation",
            contents=prompt,
            config=genai.types.GenerateContentConfig(
                response_modalities=["IMAGE", "TEXT"],
            ),
        )

        for part in response.candidates[0].content.parts:
            if part.inline_data is not None:
                image = Image.open(io.BytesIO(part.inline_data.data))
                image.save(str(output_path), "WEBP", quality=85)
                print(f"  Saved {filename} ({image.size[0]}x{image.size[1]})")
                return True

        print(f"  Warning: No image in response for {filename}")
        return False

    except Exception as e:
        print(f"  Error generating {filename}: {e}")
        return False


def main():
    print(f"Generating {len(IMAGES)} images for Chains for Hebb...")
    print(f"Output: {OUTPUT_DIR}\n")

    success = 0
    for img in IMAGES:
        if generate_image(img["prompt"], img["filename"]):
            success += 1

    print(f"\nDone: {success}/{len(IMAGES)} images generated")
    if success < len(IMAGES):
        print("Re-run to retry failed images (existing ones are skipped)")


if __name__ == "__main__":
    main()
