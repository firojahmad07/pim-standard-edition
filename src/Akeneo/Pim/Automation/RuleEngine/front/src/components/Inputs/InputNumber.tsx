import React from 'react';
import {Input, InputProps} from './Input';

const InputNumber = React.forwardRef<HTMLInputElement, InputProps>(
  (props, forwardedRef: React.Ref<HTMLInputElement>) => {
    const {hasError, className, ...remainingProps} = props;
    return (
      <Input
        className={
          className ??
          `AknTextField AknNumberField${hasError ? ' AknTextField--error' : ''}`
        }
        type='number'
        ref={forwardedRef}
        {...remainingProps}
      />
    );
  }
);

InputNumber.displayName = 'InputNumber';

export {InputNumber};
