import{c as N,a as E,j as l}from"./utils-q4EKThuO.js";import{B as I}from"./badge-DghL8o_I.js";import{C as S}from"./currency-CQP0FS4i.js";import{a as O,m as j}from"./index-CSrV_1Wb.js";import"./iframe-DOYQQv_f.js";import"./preload-helper-PPVm8Dsz.js";import"./index-BZoqsebz.js";const h=k=>{const e=N.c(24),{closing:s,className:g,onClick:t,selected:v}=k,x=v&&"bg-accent",f=t&&"cursor-pointer";let o;e[0]!==g||e[1]!==x||e[2]!==f?(o=E("flex items-center justify-between gap-3 px-3 py-2 rounded-md","hover:bg-accent transition-colors",x,f,g),e[0]=g,e[1]=x,e[2]=f,e[3]=o):o=e[3];const C=t?"button":void 0,_=t?0:void 0;let n;e[4]!==t?(n=t?y=>y.key==="Enter"&&t():void 0,e[4]=t,e[5]=n):n=e[5];let r;e[6]!==s.ct_number?(r=l.jsx("p",{className:"text-xs font-mono text-muted-foreground",children:s.ct_number}),e[6]=s.ct_number,e[7]=r):r=e[7];let a;e[8]!==s.opening_amount?(a=l.jsx("p",{className:"text-sm font-semibold",children:l.jsx(S,{value:s.opening_amount})}),e[8]=s.opening_amount,e[9]=a):a=e[9];let c;e[10]!==r||e[11]!==a?(c=l.jsxs("div",{className:"min-w-0",children:[r,a]}),e[10]=r,e[11]=a,e[12]=c):c=e[12];const b=s.status==="OPEN"?"default":"secondary";let i;e[13]!==s.status||e[14]!==b?(i=l.jsx(I,{variant:b,className:"text-xs shrink-0",children:s.status}),e[13]=s.status,e[14]=b,e[15]=i):i=e[15];let m;return e[16]!==t||e[17]!==i||e[18]!==o||e[19]!==C||e[20]!==_||e[21]!==n||e[22]!==c?(m=l.jsxs("div",{className:o,onClick:t,role:C,tabIndex:_,onKeyDown:n,children:[c,i]}),e[16]=t,e[17]=i,e[18]=o,e[19]=C,e[20]=_,e[21]=n,e[22]=c,e[23]=m):m=e[23],m};h.__docgenInfo={description:"",methods:[],displayName:"ClosingListItem"};const $={title:"Elements/Closing/ClosingListItem",component:h,tags:["autodocs"],parameters:{layout:"padded"}},d={args:{closing:j}},p={args:{closing:O}},u={args:{closing:j,selected:!0}};d.parameters={...d.parameters,docs:{...d.parameters?.docs,source:{originalSource:`{
  args: {
    closing: mockClosing
  }
}`,...d.parameters?.docs?.source}}};p.parameters={...p.parameters,docs:{...p.parameters?.docs,source:{originalSource:`{
  args: {
    closing: mockClosingClosed
  }
}`,...p.parameters?.docs?.source}}};u.parameters={...u.parameters,docs:{...u.parameters?.docs,source:{originalSource:`{
  args: {
    closing: mockClosing,
    selected: true
  }
}`,...u.parameters?.docs?.source}}};const q=["Open","Closed","Selected"];export{p as Closed,d as Open,u as Selected,q as __namedExportsOrder,$ as default};
