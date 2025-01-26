import {useLayoutEffect, useReducer, useState} from 'react';
import {
  addVisibleAttributeOptionAction,
  removeVisibleAttributeOptionAction,
  visibleAttributeOptionsReducer,
} from '../../reducer/AttributeEditForm/visibleAttributeOptionsReducer';

export const useVisibleAttributeOptions = () => {
  const [visibleOptions, dispatch] = useReducer(visibleAttributeOptionsReducer, []);
  const [renderingCount, setRenderingCount] = useState<number>(0);

  useLayoutEffect(() => {
    const container = document.querySelector('.edit-form');
    const observer = new MutationObserver(mutations => {
      mutations.forEach(mutation => {
        const element = mutation.target as Element;
        if (
          element.hasAttribute('data-attribute-option-role') &&
          element.getAttribute('data-attribute-option-role') === 'list' &&
          element.querySelectorAll('tr[data-attribute-option-role="item"]').length > 0
        ) {
          setRenderingCount(renderingCount + 1);
        }
      });
    });

    if (container) {
      observer.observe(container, {
        childList: true,
        subtree: true,
      });
    }

    return () => {
      observer.disconnect();
    };
  }, []);

  useLayoutEffect(() => {
    const elements = document.querySelectorAll('tr[data-attribute-option-role="item"]');
    const container = document.querySelector('div[data-attribute-option-role="list"]');

    const observer = new IntersectionObserver(
      function(entries) {
        entries.forEach(entry => {
          const codeElement = entry.target.querySelector('[data-attribute-option-role="item-code"]');
          if (!codeElement) {
            return;
          }
          const option = codeElement.textContent;

          if (!option) {
            return;
          }

          if (entry.isIntersecting) {
            dispatch(addVisibleAttributeOptionAction(option));
          } else {
            dispatch(removeVisibleAttributeOptionAction(option));
          }
        });
      },
      {
        root: container,
        rootMargin: '350px 0px',
      }
    );

    elements.forEach(element => {
      observer.observe(element);
    });

    return () => {
      elements.forEach(element => {
        observer.unobserve(element);
      });
      observer.disconnect();
    };
  }, [renderingCount]);

  return {
    visibleOptions,
  };
};
